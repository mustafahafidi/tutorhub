<?php
use TutorHub\Config;
use TutorHub\ImageUpload;
use TutorHub\Session;
use TutorHub\Subject;
use TutorHub\Teaching;
use TutorHub\User;
use Utils\Arrays;

if (!Session::isLogged()) die();

$u = Session::getUser();
$db = \TutorHub\getDb();

$fields = [
    'firstname' => 'name',
    'lastname' => 'surname',
    'description' => 'bio',
    'city' => 'city'
];

$modified = false;
$userModified = false;
foreach ($fields as $k => $v) {
    if (isset($_POST[$k])) {
        $content = trim($_POST[$k]);
        if (!empty($content)) {
            $u[$v] = $content;
            $userModified = true;
        }
    }
}

if (isset($_FILES['profilepic'], $_FILES['profilepic']['error']) && $_FILES['profilepic']['error'] !== UPLOAD_ERR_NO_FILE) {
    $ret = ImageUpload::upload($_FILES['profilepic']);
    switch ($ret['error']) {
        case ImageUpload::ERROR_OK:
            $u['photoUrl'] = Config::get()['upload_webroot'] . '/' . $ret['fname'];
            $userModified = true;
            break;
        // TODO handle errors
    }
}

if ($u['type'] == User::TYPE_TUTOR && isset($_POST['subjects']) && is_array($_POST['subjects'])) {
    $postTeachs = [];
    foreach ($_POST['subjects'] as $teaching) {
        if (isset($teaching['name'], $teaching['price']) && !empty($teaching['name']) && !empty($teaching['price']))
            $postTeachs[$teaching['name']] = $teaching['price'];
    }

    if (!empty($postTeachs)) {
        $subjects = Subject::where($db, array_map(function ($name) {
            return ['name' => $name];
        }, array_keys($postTeachs)));
        $newSubjects = $postTeachs;
        foreach ($subjects as $subject)
            unset($newSubjects[$subject['name']]);
        $newSubjects = array_map(function ($name) use (&$db) {
            $s = new Subject($db);
            $s['name'] = $name;
            return $s;
        }, array_keys($newSubjects));
        foreach ($newSubjects as $subject) {
            $subject->commit();
            $subjects[$subject->getKey()] = $subject;
        }
        $subjectsByName = [];
        foreach ($subjects as $subject)
            $subjectsByName[$subject['name']] = $subject;

        $teachings = array_values(Teaching::where($db, array_map(function ($subject) use (&$u) {
            return [
                'subject' => $subject->getKey(),
                'tutor' => $u->getKey()
            ];
        }, $subjects)));
        $teachingsByName = [];
        foreach ($teachings as $teaching)
            $teachingsByName[$subjects[$teaching['subject']]['name']] = $teaching;
        foreach ($postTeachs as $name => $price) {
            $teaching = $teachingsByName[$name] ?? new Teaching($db);
            $teaching['subject'] = $subjectsByName[$name]->getKey();
            $teaching['tutor'] = $u->getKey();
            $teaching['price'] = $price;
            $teaching->commit();
        }

        $modified = true;
    }
}

$modified = $modified || $userModified;
if ($modified) {
    if ($userModified)
        $u->commit();
    // TODO show success
}

$rating = number_format($u['ratingAvg'] ?? 0, 1);

if ($u['type'] == User::TYPE_TUTOR) {
    $teachings = Teaching::where($db, [['tutor' => $u->getKey()]]);
    $subjects = Subject::fromKeys($db, Arrays::column($teachings, 'subject'));
    $teachInfo = [];
    foreach ($teachings as $teaching) {
        $teachInfo[] = [
            'name' => $subjects[$teaching['subject']]['name'],
            'price' => $teaching['price']
        ];
    }
}

$title='Modifica profilo';
include("header.php");
?>
            <div id="link-to-content" class="profile container">

               <h1 class="page-title">Modifica il tuo profilo</h1>
               <aside class="alert">
                	<p>Qui puoi modificare le informazioni contenute nel tuo profilo: dati personali e materie che insegni.</p>
                </aside>
                <section class="profile-info card">
                    <img src="<?=empty($u['photoUrl'])?'img/profile.jpg':$u['photoUrl']?>" alt="foto profilo"/>
                    <h2><?=$u['name']?> <?=$u['surname']?></h2>
                    <ul>
                        <li><span>Tipologia account: </span> <?=$u['type']==User::TYPE_TUTOR?'Tutor':'Studente'?></li>
                        <li><span>Valutazioni: </span> <?=$rating?>/5.0</li>
                        <li><span>Email: </span> <?=$u['email']?></li>
                        <?php if (!empty($u['phone'])) { ?>
                            <li><span>Numero di telefono: </span> <?=$u['phone']?></li>
                        <?php } ?>
                    </ul>
                </section>
               <section class="profile-modify card">
	                <h4 class="card-title">Informazioni Profilo</h4>

	               	<div class="profileform">
	                    <form action="" method="post" accept-charset="utf-8" enctype="multipart/form-data">
	                        <fieldset>	
	                        	<legend>Inserisci i dati che vuoi modificare</legend>                            
	                            <label for="firstname" class="visuallyhidden">Nome:</label>
	                                <input type="text" placeholder="Inserisci nome" id="firstname" name="firstname" tabindex="11">

	                            <label for="lastname" class="visuallyhidden">Cognome:</label>
	                                <input type="text" placeholder="Inserisci cognome" id="lastname" name="lastname" tabindex="12">
	                            <label for="city" class="visuallyhidden">Città:</label>
	                                <input type="text" placeholder="Inserisci città" id="city" name="city" tabindex="13">
                                
	                            <label for="description" class="visuallyhidden">Descrizione:</label>
	                               	<textarea id="description" name="description" rows="5" placeholder="Inserisci una descrizione da visualizzare sul tuo profilo" tabindex="15"></textarea>

                                <?php
                                if ($u['type'] == User::TYPE_TUTOR) {
                                    $i = 0;
                                    foreach ($teachInfo as $info) {
                                ?>
                                <div class="subjectgroup">
                                    <label for="subjects[<?=$i?>][name]" class="visuallyhidden">Materia:</label>
                                    <input type="text" placeholder="Nome Materia" id="subjects[<?=$i?>][name]" name="subjects[<?=$i?>][name]" class="subject" tabindex="14" value="<?=$info['name']?>">
                                    <label for="subjects[<?=$i?>][price]" class="visuallyhidden">Prezzo:</label>
                                    <input type="text" placeholder="Prezzo/h" id="subjects[<?=$i?>][price]" name="subjects[<?=$i?>][price]" class="price" tabindex="14" value="<?=$info['price']?>">
                                </div>
                                <?php $i++; } } ?>
                                <div class="subjectgroup">
                                    <label for="subjects[<?=$i?>][name]" class="visuallyhidden">Materia:</label>
                                    <input type="text" placeholder="Nome Materia" id="subjects[<?=$i?>][name]" name="subjects[<?=$i?>][name]" class="subject" tabindex="14">
                                    <label for="subjects[<?=$i?>][price]" class="visuallyhidden">Prezzo:</label>
                                    <input type="text" placeholder="Prezzo/h" id="subjects[<?=$i?>][price]" name="subjects[<?=$i?>][price]" class="price" tabindex="14">
                                </div>
                                <a href="" class="add-subject" data-subjects="<?=$i?>" title="Aggiungi nuova materia con prezzo">+</a>

	                            <label for="profilepic" class="">Foto profilo:</label>
								<input type="file" id="profilepic" name="profilepic" tabindex="16">
	                            
	                        </fieldset>
	                        <button class="btn btn-3d btn-blue" tabindex="17">Aggiorna dati profilo</button>

	                    </form>
	                </div>
               </section>
               
            </div>
<?php include("footer.php"); ?>