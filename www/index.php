<?php
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ParameterBag;

require_once __DIR__.'/../vendor/autoload.php';

$yaml = new Symfony\Component\Yaml\Parser();
$config = $yaml->parse(file_get_contents(__DIR__ . '/../config.yml'));

$app = new Application();

$app->register(new Silex\Provider\TwigServiceProvider(), array(
  'twig.path' => __DIR__.'/../src/views',
  'twig.options' => array(
    'cache' => __DIR__.'/../src/cache',
    'auto_reload' => ($config['environment'] == "dev")
  )
));

if($config['environment'] == "dev") {
  $app['debug'] = true;
}

$app->before(function (Request $request) {
  if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
    $data = json_decode($request->getContent(), true);
    $request->request->replace(is_array($data) ? $data : array());
  }
});

$linkedin = new lib\Linkedin($config);

$app->get('/', function() use ($config, $app) {
  return $app['twig']->render('home.html.twig');
});

$app->get('/identity', function() use ($linkedin, $app) {
  return $app->json($linkedin->getName(), 200);
});

$app->get('/social', function() use ($linkedin, $app) {
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
  return $app->json($data, 200);
});

$app->get('/skills', function() use ($linkedin, $app) {
  $data = array();
  try {
    $highlight = array('php', 'drupal', 'git', 'symfony', 'open source', 'mysql', 'cms', 'linux', 'javascript', 'lamp', 'jquery');
    $skills = $linkedin->getSkills();
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
  return $app->json($data, 200);
});

$app->get('/positions', function() use ($linkedin, $app) {
  $data = array();
  try {
    $positions = $linkedin->getPositions();
    if(is_array($positions)) {
      foreach($positions as $position) {
        $data[] = $position;
      }
    }
  } catch(Exception $e) {
    $error = true;
    $data[]  = $e->getMessage();
  }
  return $app->json($data, 200);
});

$app->run();
