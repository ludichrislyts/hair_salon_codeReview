<?php
    require_once __DIR__."/../vendor/autoload.php";
    require_once __DIR__."/../src/Stylist.php";
    require_once __DIR__."/../src/Client.php";


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
        return $app['twig']->render('clients.html.twig', array('stylist' => $stylist, 'clients' => Client::findByStylistId($id)));
    });

    // STYLISTS PAGE - CONFIRMS ADDING OF STYLIST AND PROVIDES HOME PAGE LINK
    // route = page displays from home page after user adds a stylist
    $app->post('/stylist_added', function() use ($app){
        $stylist = new Stylist($_POST['name']);
        $stylist->save();
        return $app['twig']->render('stylist_added.html.twig', array('stylist' => Stylist::find($stylist->getId())));
    });

    // page to edit a stylist
    // route = page displays after user clicks "edit stylist" from that stylist's page
    $app->get('/stylist/{id}/update', function($id) use ($app){
        $stylist = stylist::find($id);
        // $name = $_POST['name'];
        // $stylist->update($name);
        return $app['twig']->render('edit_stylist.html.twig', array('stylist' => $stylist));
    });

    //page that renders after an update to stylist, displays that stylists clients
    $app->patch("/stylist/{id}", function($id) use ($app){
        $name = $_POST['name'];
        $stylist = Stylist::find($id);
        $stylist->update($name);
        return $app['twig']->render('clients.html.twig', array('stylist' => $stylist, 'clients' => Client::getAll()));
    });

    // DELETE STYLIST PAGE - CONFIRMS DELETE OF STYLIST AND LINK TO HOME PAGE
    $app->delete('/stylist/{id}', function($id) use ($app) {
        $stylist = stylist::find($id);
        $stylist->deleteOne($id);
        return $app['twig']->render('index.html.twig', array('stylists' => Stylist::getAll()));
        //, array('stylists' => stylist::getAll()));
    });

    // ADDED CLIENT TO STYLIST RESULT
    $app->post('/stylist_clients', function() use ($app){
        $stylist_id = $_POST['stylist_id'];
        $client = new Client($_POST['name'], $stylist_id);
        $client->save();
        //var_dump($client);
        $stylist_clients = Client::findByStylistID($stylist_id);
        return $app['twig']->render('clients.html.twig', array('clients' => $stylist_clients, 'stylist' => Stylist::find($stylist_id)));
    });

    $app->get('/client/{id}/update', function($id) use ($app){
        $client = Client::findByClientId($id);
        // $name = $_POST['name'];
        // $stylist->update($name);
        return $app['twig']->render('edit_client.html.twig', array('client' => $client));
    });

    // UPDATE TO CLIENT
    $app->patch("/client/{id}", function($id) use ($app){
        $name = $_POST['name'];
        var_dump($name);
        $client = Client::findByClientId($id);
        $client->setName($name);
        var_dump($client);
        $stylist_id = $client->getStylistId();
        $stylist = Stylist::find($stylist_id);
        return $app['twig']->render('clients.html.twig', array('stylist' => $stylist, 'clients' => Client::findByStylistId($stylist_id)));
    });

    return $app;


?>
