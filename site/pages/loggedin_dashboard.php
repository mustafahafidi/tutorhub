<?php
use TutorHub\Booking;
use TutorHub\Session;
use TutorHub\Slot;
use TutorHub\Subject;
use TutorHub\Teaching;
use TutorHub\User;
use Utils\Arrays;

if (!Session::isLogged()) die();

$u = Session::getUser();
$t = $u['type'] == User::TYPE_TUTOR;

$db = \TutorHub\getDb();

if (isset($_POST['request'], $_POST['id'])) {
    $booking = Booking::fromKey($db, $_POST['id']);
    if ($booking !== null) {
        $modified = false;
        switch ($_POST['request']) {
            case 'accept':
                $booking['status'] = Booking::STATUS_ACCEPTED;
                $modified = true;
                break;
            case 'refuse':
                $booking['status'] = Booking::STATUS_DECLINED;
                $modified = true;
                break;
        }
        if ($modified)
            $booking->commit();
    }
}

if ($u['type'] == User::TYPE_STUDENT &&
    isset($_POST['slot'], $_POST['message'], $_POST['teaching'])) {

    $booking = new Booking($db);
    $booking['student'] = $u->getKey();
    $booking['slot'] = $_POST['slot'];
    $booking['teaching'] = $_POST['teaching'];
    $booking['status'] = Booking::STATUS_PENDING;
    $booking['comment'] = $_POST['message'];
    $booking->commit();
}

if ($t) {
    $teachings = Teaching::where($db, [['tutor' => $u->getKey()]]);
    $bookings = Booking::where($db, Arrays::map(
        function ($x) { return ['teaching' => $x->getKey()]; },
        $teachings));
    $users = User::fromKeys($db, Arrays::column($bookings, 'student'));
} else {
    $bookings = Booking::where($db, [['student' => $u->getKey()]]);
    $teachings = Teaching::fromKeys($db, Arrays::column($bookings, 'teaching'));
    $users = User::fromKeys($db, Arrays::column($teachings, 'tutor'));
}

$subjects = Subject::fromKeys($db, Arrays::column($teachings, 'subject'));
$slots = Slot::fromKeys($db, Arrays::column($bookings, 'slot'));

$bookInfoReq = [];
$bookInfoSched = [];
foreach ($bookings as $booking) {
    $teaching = $teachings[$booking['teaching']];
    $slot = $slots[$booking['slot']];
    $info = [
        'book' => $booking,
        'teach' => $teaching,
        'user' => $users[$t ? $booking['student'] : $teaching['tutor']],
        'subject' => $subjects[$teaching['subject']],
        'start' => new DateTime($slot['start']),
        'end' => new DateTime($slot['end'])
    ];
    if ($booking['status'] == Booking::STATUS_ACCEPTED)
        $bookInfoSched[] = $info;
    else
        $bookInfoReq[] = $info;
}

function statusToText($status)
{
    switch ($status) {
        case Booking::STATUS_ACCEPTED:
            return 'Approvata';
        case Booking::STATUS_DECLINED:
            return 'Rifiutata';
    }
    return 'In attesa';
}

function statusToClass($status)
{
    switch ($status) {
        case Booking::STATUS_ACCEPTED:
            return 'approved';
        case Booking::STATUS_DECLINED:
            return 'rejected';
    }
    return 'waiting';
}

$title = 'Dashboard';
include("header.php");
?>
            <div id="link-to-content" class="dashboard container">
                <h1 class="page-title">Dashboard</h1>
                <aside class="alert">
                    <?php if ($t) { ?>
                        <p>Qui trovi le richieste di lezioni che hai ricevuto, che puoi approvare o rifiutare. Inoltre puoi vedere i tuoi prossimi impegni al volo!</p>
                    <?php } else { ?>
                        <p>Qui trovi le richieste di lezioni che hai inviato. Inoltre puoi vedere le tue prossime lezioni prenotate.</p>
                    <?php } ?>
                </aside>
                <article class="card">
	                <h4 class="card-title">Richieste di lezioni <?=$t?'ricevute':'inviate'?></h4>

                    <?php $count = 1; foreach ($bookInfoReq as $info) { ?>
                	<section class="request" aria-expanded="false">
                        <h2 class="visuallyhidden">Richiesta <?=$count?></h2>
                        <div class="user">
                            <div class="user-pic"><img src="<?=empty($info['user']['photoUrl'])?'img/profile.jpg':$info['user']['photoUrl']?>" alt=""></div>
                            <div class="user-text">
                                <a href="index.php?page=view-profile&id=<?=$info['user']->getKey()?>" title="<?=$info['user']['name']?> <?=$info['user']['surname']?>">
                                    <span class="visuallyhidden"> Nome utente </span><?=$info['user']['name']?> <?=$info['user']['surname']?>
                                </a>
                                <p class="date">
                                    <span class="visuallyhidden">
                                    Data: </span><?=$info['start']->format('d/m/Y')?><span class="state <?=statusToClass($info['book']['status'])?>">
                                    <span class="visuallyhidden"> Stato richiesta: </span><?=statusToText($info['book']['status'])?></span>
                                </p>
                            </div>
                            <a href="index.php?page=view-profile&id=<?=$info['user']->getKey()?>" title="Vai al profilo di <?=$info['user']['name']?>" class="btn btn-dark btn-3d btn-rounded hidden" aria-hidden="true">Visualizza profilo</a>
                            <i class="ti-arrows-corner" aria-hidden="true"></i>
                        </div>
                        <span class="visuallyhidden"> Dettagli richiesta: </span>
                        <ul class="info">
                            <li><span>Ore:</span><span class="visuallyhidden"> da </span> <?=$info['start']->format('H:i')?> -<span class="visuallyhidden"> a </span> <?=$info['end']->format('H:i')?></li>
                            <li><span>Città:</span> <?=$info['user']['city']?></li>
                            <li><span>Materia:</span> <?=$info['subject']['name']?></li>
                        </ul>
                        <span class="visuallyhidden"> Messaggio: </span>
                        <p class="desc"><?=$info['book']['comment']?></p>

                        <div class="acceptform visuallyhidden">
                            <form action="index.php?page=dashboard" method="post" accept-charset="utf-8">
                                <fieldset>
                                    <legend>Vuoi accettare o rifiutare la richiesta di lezione?</legend>
                                    <input type="hidden" aria-hidden="true" value="<?=$info['book']->getKey()?>" name="id">
                                    <label for="request-refuse" class="visuallyhidden">Rifiuta la richiesta</label>
                                    <button value="refuse" id="request-refuse" name="request" class="btn btn-3d btn-danger">Rifiuta</button>
                                    <label for="request-accept" class="visuallyhidden">Accetta la richiesta</label>
                                    <button value="accept" id="request-accept" name="request" class="btn btn-3d btn-blue">Accetta</button>
                                </fieldset>
                            </form> 
                        </div>
                               
                	</section>
                    <?php $count++; } ?>
                </article>

                <article class="card">
                    <h4 class="card-title">Prossimi impegni</h4>
                    <?php $count = 1; foreach ($bookInfoSched as $info) { ?>
                        <section class="request">
                            <h2 class="visuallyhidden">Impegno <?=$count?></h2>
                            <div class="user">
                                <div class="user-pic"><img src="<?=empty($info['user']['photoUrl'])?'img/profile.jpg':$info['user']['photoUrl']?>" class="user-pic" alt=""></div>
                                <div class="user-text">
                                    <a href="index.php?page=view-profile?id=<?=$info['user']->getKey()?>" title="<?=$info['user']['name']?> <?=$info['user']['surname']?>">
                                        <span class="visuallyhidden"> Nome utente </span><?=$info['user']['name']?> <?=$info['user']['surname']?>
                                    </a>
                                    <p class="date"><span class="visuallyhidden">
                                    Data: </span><?=$info['start']->format('d/m/Y')?><span class="state <?=statusToClass($info['book']['status'])?>">
                                    <span class="visuallyhidden"> Stato richiesta: </span><?=statusToText($info['book']['status'])?></span>
                                    </p>
                                </div>
                                <a href="index.php?page=view-profile&id=<?=$info['user']->getKey()?>" title="Vai al profilo di <?=$info['user']['name']?>" class="btn btn-dark btn-3d btn-rounded hidden" aria-hidden="true">Visualizza profilo</a>
                                <i class="ti-arrows-corner" aria-hidden="true"></i>
                            </div>
                            <span class="visuallyhidden"> Dettagli richiesta: </span>
                            <ul class="info">
                                <li><span>Ore:</span><span class="visuallyhidden"> da </span> <?=$info['start']->format('H:i')?> -<span class="visuallyhidden"> a </span> <?=$info['end']->format('H:i')?></li>
                                <li><span>Città:</span> <?=$info['user']['city']?></li>
                                <li><span>Materia:</span> <?=$info['subject']['name']?></li>
                            </ul>
                            <span class="visuallyhidden"> Messaggio: </span>
                            <p class="desc"><?=$info['book']['comment']?></p>

                            <div class="acceptform visuallyhidden">
                            <form action="index.php?page=dashboard" method="post" accept-charset="utf-8">
                                <fieldset>
                                    <legend>Vuoi cancellare questa lezione?</legend>
                                    <input type="hidden" aria-hidden="true" value="<?=$info['book']->getKey()?>" name="id">
                                    <label for="request-refuse" class="visuallyhidden">Cancella la lezione</label>
                                    <button value="refuse" id="request-refuse" name="request" class="btn btn-3d btn-danger">Cancella questo appuntamento</button>
                                </fieldset>
                            </form> 
                        </div>
                        </section>
                        <?php $count++; } ?>
                </article>
                <div class="modal-close hidden" aria-hidden="true">X</div>
            </div>

<?php include("footer.php"); ?>