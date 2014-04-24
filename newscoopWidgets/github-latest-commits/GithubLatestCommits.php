<?php
/**
 * @package Newscoop\GithubLatestCommitsBundle
 * @author Rafał Muszyński <rmuszynski1@gmail.com>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * @author Rafał Muszyński
 * @description Displays latest Newscoop commits from Github.
 * @homepage http://www.sourcefabric.org
 * @version 1.0
 * @license GPLv3
 */
class GithubLatestCommits extends Widget
{
    /* @var array */
    protected $commits = array();

    /* @var string */
    protected $apiUrl;

    protected $title;

    public function __construct()
    {
        $this->translator = \Zend_Registry::get('container')->getService('translator');
        $this->title = $this->translator->trans("github.label.head", array(), 'github');
        $this->apiUrl = "https://api.github.com/";
    }

    /**
     * @return array
     * @throws \Buzz\Exception\ClientException
     */
    public function beforeRender()
    {
        try {

            $cacheService = \Zend_Registry::get('container')->getService('newscoop.cache');
            $cacheKey = 'github_commits__' . md5('github_commits__');
            $commits = array();

            if ($cacheService->contains($cacheKey)) {
                $commits = $cacheService->fetch($cacheKey);
            } else {
                $curlClient = new \Buzz\Client\Curl();
                $curlClient->setTimeout(10000);
                $browser = new \Buzz\Browser($curlClient);
                $result =  $browser->get($this->apiUrl . 'repos/sourcefabric/newscoop/commits', array('User-Agent' => 'Newscoop'));
                $commits = json_decode($result->getContent(), true);
                $cacheService->save($cacheKey, $commits, 600);
            }

            $this->commits = $this->prepareArray($commits);
        } catch(\Buzz\Exception\ClientException $e) {
            throw new \Buzz\Exception\ClientException();
        }
    }

    /**
     * @return void
     */
    public function render()
    {
        include_once dirname(__FILE__) . '/commits.php';
    }

    /**
     * Prepares array with commits
     *
     * @param array $commits Commits
     *
     * @return array
     */
    private function prepareArray($commits)
    {
        $convertedCommits = array();
        foreach ($commits as $key => $commit) {
            $commitDate = new \DateTime(date("Y-m-d H:i:s", strtotime($commit['commit']['author']['date'])));
            $dateNow = new \DateTime('now');
            $diff = $commitDate->diff($dateNow);

            if ($commit['commit']['author']['date']) {
                $string = '';
                if ($diff->d >= 1) {
                    $days = $diff->d;
                    $string .= $days + 1;

                    if ($days > 1) {
                        $string .= ' ' . $this->translator->trans('github.label.days', array(), 'github');
                    } else {
                        $string .= ' ' . $this->translator->trans('github.label.day', array(), 'github');
                    }
                } else {
                    if ($diff->h > 1) {
                        $string .= $diff->h;
                        $string .= ' ' . $this->translator->trans('github.label.hours', array(), 'github') . ' ';
                    }

                    if ($diff->h == 1) {
                        $string .= $diff->h;
                        $string .= ' ' . $this->translator->trans('github.label.hour', array(), 'github') . ' ';
                    }

                    $string .= $diff->i == 0 ? 1 : $diff->i;

                    if ($diff->i > 1) {
                        $string .= ' ' . $this->translator->trans('github.label.minutes', array(), 'github');
                    } else {
                        $string .= ' ' . $this->translator->trans('github.label.minute', array(), 'github');
                    }
                }

                $string .= ' ' . $this->translator->trans('github.label.ago', array(), 'github');

                $commit['commit']['author']['date_diff'] = $string;
            }

            $convertedCommits[$key] = $commit;
        }

        return $convertedCommits;
    }
}
