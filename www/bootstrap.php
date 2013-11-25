<?php
require __DIR__ . '/../vendor/autoload.php';

$error = false;
$data = array();

$yaml = new Symfony\Component\Yaml\Parser();
$config = $yaml->parse(file_get_contents(__DIR__ . '/../config.yml'));

$action = isset($_GET['a']) ? $_GET['a'] : "";

$l = new jief\Linkedin($config);

switch ($action) {
  case 'na':
    $data = $l->getName();
    break;
  case 'so':
    $data = array(
      array('url' => 'http://twitter.com/jief', 'label' => 'Twitter', 'key' => 'twitter'),
      array('url' => 'http://be.linkedin.com/in/jfmonfort', 'label' => 'Linked In', 'key' => 'linkedin'),
      array('url' => 'http://jief.me/+', 'label' => 'Google+', 'key' => 'plus'),
      array('url' => 'http://klout.com/jief', 'label' => 'Klout', 'key' => 'klout'),
      array('url' => 'https://foursquare.com/jief', 'label' => 'Foursquare', 'key' => '4sqr'),
      array('url' => 'http://drupal.org/user/104385', 'label' => 'Drupal', 'key' => 'drupal'),
      array('url' => 'http://instagram.com/jief_me', 'label' => 'Instagram', 'key' => 'instagram'),
      array('url' => 'http://www.diigo.com/user/jfmonfort', 'label' => 'Diigo', 'key' => 'diigo'),
      array('url' => 'mailto:hello@monfort.me', 'label' => 'Contact', 'key' => 'gmail'),
      array('url' => 'callto://jf.monfort', 'label' => 'Skype', 'key' => 'skype'),
      array('url' => 'http://last.fm/user/jief', 'label' => 'Last.fm', 'key' => 'lastfm'),
      array('url' => 'http://open.spotify.com/user/jfmonfort', 'label' => 'Spotify', 'key' => 'spotify'),
      array('url' => 'http://getpocket.com/users/jief_me/feed/all', 'label' => 'Pocket', 'key' => 'pocket'),
      array('url' => 'https://github.com/jief', 'label' => 'Github', 'key' => 'github'),
      array('url' => 'https://www.facebook.com/jf.monfort', 'label' => 'Facebook', 'key' => 'facebook')
    );
    break;
  case 'sk':
    try {
      $highlight = array('php', 'drupal', 'git', 'symfony', 'open source', 'mysql', 'cms', 'linux', 'javascript', 'lamp', 'jquery');
      $skills = $l->getSkills();
      if(is_array($data)) {
        foreach($skills as $skill) {
          $row = array('name' => $skill, 'label' => 'info');
          if(in_array(strtolower($skill), $highlight)) {
            $row['label'] = 'danger';
          }
          $data[] = $row;
        }
      }
      sort($data);
    } catch(Exception $e) {
      $error = true;
      $data[]  = $e->getMessage();
    }
    break;
  case 'po':
    try {
      $positions = $l->getPositions();
      if(is_array($positions)) {
        foreach($positions as $position) {
          $data[] = $position;
        }
      }
    } catch(Exception $e) {
      $error = true;
      $data[]  = $e->getMessage();
    }
    break;
}

header('Cache-Control: no-cache, must-revalidate');
header('Content-type: application/json');

if($error) {
  $data = array('error' => $error, 'msg' => $data[0]);
}

echo json_encode($data);
