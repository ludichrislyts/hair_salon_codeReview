<?php
    require_once __DIR__."/../vendor/autoload.php";
    require_once __DIR__."/../src/Stylist.php";
    //require_once __DIR__."/../src/Client.php";


    $app = new Silex\Application();
    $app['debug'] = true;


    $server = 'mysql:host=localhost;dbname=hair_salon';
    $username = 'root';
    $password = 'root';
    $DB = new PDO($server, $username, $password);


    $app->register(new Silex\Provider\TwigServiceProvider(), array(
        'twig.path' => __DIR__.'/../views'
    ));

    use Symfony\Component\HttpFoundation\Request;
    Request::enableHttpMethodParameterOverride();

    //HOME PAGE - DISPLAYS LIST OF STYLISTS - OPTION TO VIEW BY STYLIST OR ADD STYLIST
    $app->get("/", function() use ($app) {
        return $app['twig']->render('index.html.twig', array('stylists' => Stylist::getAll()));
    });

    // STYLIST_ID PAGE - SHOWS LIST OF CLIENTS FOR A STYLIST, AND OPTION TO ADD CLIENT OR EDIT/DELETE STYLIST
    // route = this page displays from the home page after a user clicks on a stylist name
    $app->get("/stylist/{id}", function($id) use ($app) {
        $stylist = Stylist::find($id);
        return $app['twig']->render('stylist{id}.html.twig', array('clients' => Client::getAll()));
    });

    // STYLISTS PAGE - CONFIRMS ADDING OF STYLIST AND PROVIDES HOME PAGE LINK
    // route = page displays from home page after user adds a stylist
    $app->post('/stylist_added', function() use ($app){
        $stylist = new Stylist($_POST['name']);
        $stylist->save();
        return $app['twig']->render('stylist.html.twig', array('stylist' => Stylist::find($stylist->getId())));
    });

    return $app;


?>
