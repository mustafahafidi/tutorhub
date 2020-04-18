<?php
use TutorHub\Session;
use TutorHub\Subject;
use TutorHub\Slot;
use TutorHub\Teaching;
use TutorHub\User;
use Utils\Arrays;

$u = Session::getUser();
if ($u === null || $u['type'] != User::TYPE_STUDENT) die();

$searchQuery = $_POST['search'] ?? '';
$keywords = explode(' ', $searchQuery);
$keywords = array_map('trim', $keywords);

$db = \TutorHub\getDb();

$query = User::query($db);
$first = true;
foreach ($keywords as $keyword) {
    $pat = "%$keyword%";
    if ($first)
        $query->where('name', $pat, 'like');
    else
        $query->orWhere('name', $pat, 'like');
    $query->orWhere('surname', $pat, 'like');
    $query->orWhere('biography', $pat, 'like');
    $query->orWhere('city', $pat, 'like');
    $first = false;
}
$tutors = $query->get();

$teachings = Teaching::where($db, array_map(function ($tutorKey) {
    return ['tutor' => $tutorKey];
}, array_keys($tutors)));
$subjects = Subject::fromKeys($db, Arrays::column($teachings, 'subject'));
$slots = Slot::where($db, array_map(function ($tutorKey) {
    return ['tutor' => $tutorKey];
}, array_keys($tutors)));

foreach ($tutors as $k => $t) {
    $teachInfo[$k] = [];
    foreach ($teachings as $teaching) {
        $teachInfo[$k][] = [
            'teaching' => $teaching,
            'subject' => $subjects[$teaching['subject']]['name'],
            'price' => $teaching['price']
        ];
    }
}

$title = 'Risultati';
include("header.php");
?>
            <div class="search container">

                <h1 class="page-title">Cerca Tutor - risultati per <?=$searchQuery?></h1>
                <aside class="alert">
                	<p>Qui puoi effettuare una ricerca per trovare il tutor più adatto a te!</p>
                </aside>
                <div class="searchform">
	                <form action="" method="post" accept-charset="utf-8">
	                	<fieldset>
	                		<legend>Inserisci il nome di una materia o città</legend>
	                		<label for="search" class="visuallyhidden">Nome:</label>
	                           	<input type="text" placeholder="Inserisci materia o città" id="search" name="search" value="<?=$searchQuery?>">
	                           	<button class="btn btn-blue">Cerca</button>
	                	</fieldset>
	                </form>
	            </div>
	            <article class="results card">
	                <h4 class="card-title">Ecco i tutor che abbiamo trovato per te</h4>
                    <?php
                    foreach ($tutors as $t) {
                        $rating = number_format($u['ratingAvg'] ?? 0, 1);
                    ?>
                	<section class="result" aria-expanded="false">
	                    	<div class="user">
	                    		<div class="user-pic"><img src="<?=empty($t['photoUrl'])?'img/profile.jpg':$t['photoUrl']?>" class="user-pic" alt="utente"></div>
		                        <div class="user-text">
		                        	<a href="index.php?page=view-profile&id=<?$t->getKey()?>"><?=$t['name']?> <?=$t['surname']?></a>
	                    			<p class="rating">Valutazioni: <span class="btn btn-dark btn-sm"><?=$rating?></span></p>
		                        </div>
	                    		<a href="index.php?page=view-profile&id=<?$t->getKey()?>" title="Vai al profilo di <?=$t['name']?> <?=$t['surname']?>" class="btn btn-dark btn-3d btn-rounded hidden" aria-hidden="true">Visualizza profilo</a>
                            	<i class="ti-arrows-corner" aria-hidden="true"></i>
	                    	</div>
	                    	<ul class="info">
                                <?php if (!empty($t['city'])) { ?>
                                <li><span>Città: </span> <?=$t['city']?></li>
                                <?php } ?>
	                    		<li><span>Materie:</span> 
		                    			<ul>
                                            <?php foreach($teachInfo[$t->getKey()] as $info) { ?>
                                            <li><?=$info['subject']?> (<?=$info['price']?>€/h)</li>
                                            <?php } ?>
			                    		</ul>
	                    		</li>
	                    	</ul>
	                    	<p class="desc"><?=$t['bio']?></p>

	                        <div class="acceptform visuallyhidden">
                            <form action="index.php?page=dashboard" method="post" accept-charset="utf-8">
                                <fieldset>
                                    <legend>Se vuoi prenotare una lezione con questo tutor, scegli uno slot di disponibilità del tutor ed inviagli una richiesta.</legend>

                                    <label for="subjects" class="visuallyhidden">Disponibilità del tutor:</label>
	                                    <select id="subjects" name="teaching">
                                            <?php foreach ($teachInfo[$t->getKey()] as $info) { ?>
                                            <option value="<?=$info['teaching']->getKey()?>"><?=$info['subject']?> (<?=$info['price']?>€/h)</option>
                                            <?php } ?>
										</select>

	                    			<label for="slots" class="visuallyhidden">Disponibilità del tutor:</label>
	                                    <select id="slots" name="slot">
                                        <?php
                                            $ts = array_filter($slots, function ($slot) use (&$t) {
                                                return $slot['tutor'] == $t->getKey();
                                            });
                                            foreach ($ts as $slot) {
                                                $start = new DateTime($slot['start']);
                                                $end = new DateTime($slot['end']);
                                        ?>
										  <option value="<?=$slot->getKey()?>"><?=$start->format('d/m/Y H:i')?> - <?=$end->format('H:i')?></option>
                                        <?php } ?>
										</select>
	                    			<label for="message" class="visuallyhidden">Messaggio:</label>
	                               		<textarea id="message" name="message" rows="5" placeholder="Inserisci messaggio da inviargli: ad esempio potresti dire che cosa vorresti imparare" tabindex="15"></textarea>
                                    <button type="submit" class="btn btn-3d btn-blue">Invia la richiesta</button>
                                </fieldset>
                            </form> 
                        </div>
                	</section>
                    <?php } ?>

                </article>
	            <div class="modal-close hidden" aria-hidden="true">X</div>
            </div>

<?php include("footer.php"); ?>