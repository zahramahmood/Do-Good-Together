<?php

require('../vendor/autoload.php');

$app = new Silex\Application();
$app['debug'] = true;

// Register the monolog logging service
$app->register(new Silex\Provider\MonologServiceProvider(), array(
  'monolog.logfile' => 'php://stderr',
));

// Register the Twig templating engine
$app->register(new Silex\Provider\TwigServiceProvider(), array(
  'twig.path' => __DIR__.'/../views',
));

// Register the Postgres database add-on
$dbopts = parse_url(getenv('DATABASE_URL'));
$app->register(new Herrera\Pdo\PdoServiceProvider(),
  array(
    'pdo.dsn' => 'pgsql:dbname='.ltrim($dbopts["path"],'/').';host='.$dbopts["host"],
    'pdo.port' => $dbopts["port"],
    'pdo.username' => $dbopts["user"],
    'pdo.password' => $dbopts["pass"]
  )
);

//////////////////////////////////////////////////////////
// ROUTES
//////////////////////////////////////////////////////////
$app->get('/', function() use($app) {
  $app['monolog']->addDebug('logging output.');
  return $app['twig']->render('homepage.html', array());
});

$app->get('/db/', function() use($app) {
  $st = $app['pdo']->prepare('SELECT name FROM test_table');
  $st->execute();

  $names = array();
  while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
    $app['monolog']->addDebug('Row ' . $row['name']);
    $names[] = $row;
  }

  return $app['twig']->render('database.twig', array(
    'names' => $names
  ));
});

$app->get('/twig/{name}', function($name) use($app) {
  return $app['twig']->render('index.twig', array(
    'name' => $name,
  ));
});

$app->get('/opportunities/', function() use($app) {
  return $app['twig']->render('opportunities.html', array());
});

$app->get('/connect/', function() use($app) {
  return $app['twig']->render('connect.html', array());
});

$app->get('/profile/', function() use($app) {
  return $app['twig']->render('profile.html', array());
});

$app->get('/invite/', function() use($app) {
  return $app['twig']->render('invite.html', array());
});

$app->get('/sent/{name}', function($name) use($app) {
  return $app['twig']->render('sent.html', array(
      'name' => $name,
    ));
});

$app->get('/calendar/', function() use($app) {
  return $app['twig']->render('calendar.html', array());
});

$app->get('/invitations/', function() use($app) {
  return $app['twig']->render('invitations.html', array());
});

$app->get('/matches/', function() use($app) {
  return $app['twig']->render('matches.html', array());
});
//////////////////////////////////////////////////////////
// END OF ROUTES
//////////////////////////////////////////////////////////

$app->run();

?>
