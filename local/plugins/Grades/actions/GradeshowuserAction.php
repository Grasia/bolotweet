<?php

/**
 * 
 * BoloTweet 2.0
 *
 * @author   Alvaro Ortego <alvorteg@ucm.es>
 *
 */
class GradeshowuserAction extends Action {

    var $alumno = null;
    var $group = null;
    var $page = null;
    var $notice = null;
    var $error = null;
    // Variables para estadísticas

    var $numeroTweets = null;
    var $notaMedia = null;
    var $notaTotal = null;
    var $numeroTweetsPuntuados = null;

    /**
     * Take arguments for running
     *
     * This method is called first, and it lets the action class get
     * all its arguments and validate them. It's also the time
     * to fetch any relevant data from the database.
     *
     * Action classes should run parent::prepare($args) as the first
     * line of this method to make sure the default argument-processing
     * happens.
     *
     * @param array $args $_REQUEST args
     *
     * @return boolean success flag
     */
    function prepare($args) {
        parent::prepare($args);

        $alumno = $this->trimmed('nickname');
        $group = $this->trimmed('nickgroup');

        $this->group = User_group::staticGet('nickname', $group);
        $this->alumno = Profile::staticGet('nickname', $alumno);

        $this->page = ($this->arg('page')) ? ($this->arg('page') + 0) : 1;

        $ids = Grades::getNoticeFromUserInGroup($this->alumno->id, $this->group->id);

        $this->generarEstadisticas($ids);

        $this->notice = $this->getNotices(($this->page - 1) * NOTICES_PER_PAGE, NOTICES_PER_PAGE + 1, $ids);

        common_set_returnto($this->selfUrl());

        return true;
    }

    function showPageNotice() {
        if ($this->error) {
            $this->element('p', 'error', $this->error);
        }
    }

    function getNotices($offset, $limit, $ids) {

        if (empty($ids)) {
            $this->error = "El usuario " . $this->alumno->nickname . " no ha publicado nada en " . $this->group->nickname;
            return;
        }

        $total = $offset + $limit;

        for ($i = $offset; $i < $total; $i++) {
            $idsFinal[] = $ids[$i];
        }

        $notices = Notice::multiGet('id', $idsFinal);

        return $notices;
    }

    function title() {

        if ($this->page == 1) {
            // TRANS: Page title for first group page. %s is a group name.
            return sprintf(_('Tweets puntuados de %s en %s'), $this->alumno->nickname, strtoupper($this->group->nickname));
        } else {
            // TRANS: Page title for any but first group page.
            // TRANS: %1$s is a group name, $2$s is a page number.
            return sprintf(_('Tweets puntuados de %1$s en %2$s (%3$d)'), $this->alumno->nickname, strtoupper($this->group->nickname), $this->page);
        }
    }

    /**
     * Handle request
     *
     * This is the main method for handling a request. Note that
     * most preparation should be done in the prepare() method;
     * by the time handle() is called the action should be
     * more or less ready to go.
     *
     * @param array $args $_REQUEST args; handled in prepare()
     *
     * @return void
     */
    function handle($args) {
        parent::handle($args);

        $this->showPage();
    }

    function showContent() {
        $this->showUserNotices();
    }

    function showUserNotices() {

        if ($this->error) {
            $this->elementStart('p');
            $this->raw('Volver a <a href=' . common_local_url('gradereport') . '>Grade Reports</a>.');
            $this->elementEnd('p');
        } else {
            $nl = new NoticeList($this->notice, $this);
            $cnt = $nl->show();
            $this->pagination($this->page > 1, $cnt > NOTICES_PER_PAGE, $this->page, 'gradeshowuser', array('nickgroup' => $this->group->nickname, 'nickname' => $this->alumno->nickname));
        }
    }

    function showProfileBlock() {
        $block = new AccountProfileBlock($this, $this->alumno);
        $block->show();
    }

    function showSections() {
        $user = common_current_user();

        parent::showSections();

        if ($user->hasRole('grader')) {
            $this->showStatistics();
        }
    }

    function showStatistics() {

        $this->elementStart('div', array('id' => 'entity_statistics',
            'class' => 'section'));

        // TRANS: H2 text for user statistics.
        $this->element('h2', null, 'Estadísticas');

        $this->elementStart('p');
        $this->raw('Número de Tweets: ');
        $this->elementStart('span', array('class' => 'statistics-span'));
        $this->raw($this->numeroTweets);
        $this->elementEnd('span');
        $this->elementEnd('p');

        $this->elementStart('p');
        $this->raw('Tweets Puntuados: ');
        $this->elementStart('span', array('class' => 'statistics-span'));
        $this->raw($this->numeroTweetsPuntuados);
        $this->elementEnd('span');
        $this->elementEnd('p');

        $this->elementStart('p');
        $this->raw('Nota Media: ');
        $this->elementStart('span', array('class' => 'statistics-span'));
        $this->raw($this->notaMedia);
        $this->elementEnd('span');
        $this->elementEnd('p');

        $this->elementStart('p');
        $this->raw('Puntos Totales: ');
        $this->elementStart('span', array('class' => 'statistics-span'));
        $this->raw($this->notaTotal);
        $this->elementEnd('span');
        $this->elementEnd('p');

        $this->elementEnd('div');
    }

    function isReadOnly($args) {
        return false;
    }

    function generarEstadisticas($ids) {

        $this->numeroTweetsPuntuados = count($ids);
        $this->numeroTweets = Grades::getNumberTweetsOfUserInGroup($this->alumno->id, $this->group->id);
        $notas = Grades::getNotaMediaYTotalofUserinGroup($this->alumno->id, $this->group->id);

        $this->notaMedia = number_format(reset($notas), 2);
        $this->notaTotal = number_format(key($notas), 2);

        if (empty($this->notaTotal))
            $this->notaTotal = number_format(0, 2);
    }

}
