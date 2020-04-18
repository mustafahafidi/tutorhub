<?php
use TutorHub\Session;
use TutorHub\Subject;
use TutorHub\Teaching;
use TutorHub\User;
use Utils\Arrays;

if (!Session::isLogged()) die();

$u = null;
if (isset($_GET['id']))
    $u = User::fromKey(\TutorHub\getDb(), $_GET['id']);
$lu = Session::getUser();
$u = $u ?? $lu;

$me = $u->getKey() === $lu->getKey();
$rating = number_format($u['ratingAvg'] ?? 0, 1);

if ($u['type'] == User::TYPE_TUTOR) {
    $db = \TutorHub\getDb();
    $teachings = Teaching::where($db, [['tutor' => $u->getKey()]]);
    $subjects = Subject::fromKeys($db, Arrays::column($teachings, 'subject'));

    $teachInfo = [];
    foreach ($teachings as $teaching) {
        $teachInfo[] = [
            'subject' => $subjects[$teaching['subject']]['name'],
            'price' => $teaching['price']
        ];
    }
}

$title='Profilo';
$ou = $u;
include("header.php");
$u = $ou;
?>
        <div id="link-to-content" class="profile container">

               <h1 class="page-title"><?=$me?'Il tuo profilo':"Il profilo di {$u['name']} {$u['surname']}"?></h1>
                <?php if ($me) { ?>
               <aside class="alert">
                	<p>Questo è come gli altri utenti ti vedono</p>
                </aside>
                <?php } ?>
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
               <section class="profile-view card">
	                <h4 class="card-title">Informazioni Profilo</h4>
	                <article>
                        <h5 class="not-visible">Elenco dettagliato informazioni profilo</h5>
	                	<div class="profile-info">
	                		<ul>
		                		<li><span>Nome Completo: </span> <?=$u['name']?> <?=$u['surname']?></li>
                                <?php if ($u['type'] == User::TYPE_TUTOR) { ?>
		                		<li><span>Materie: </span>
		                			<ul>
                                        <?php foreach($teachInfo as $info) { ?>
			                			<li><?=$info['subject']?> (<?=$info['price']?>€/h)</li>
                                        <?php } ?>
			                		</ul>
                                </li>
                                <?php } ?>
                                <?php if (!empty($u['city'])) { ?>
		                		<li><span>Città: </span> <?=$u['city']?></li>
                                <?php } ?>
		                	</ul>
	                	</div>
	                	<section class="profile-info card">
                            <h4 class="">Biografia</h4>
		                	<p class="desc"><?=$u['bio']?></p>
						</section>
	                </article>
               </section>
               
            </div>
<?php include("footer.php"); ?>