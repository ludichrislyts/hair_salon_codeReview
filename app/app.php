<?php
    require_once __DIR__."/../vendor/autoload.php";
    require_once __DIR__."/../src/Stylist.php";
    require_once __DIR__."/../src/Client.php";


    $app = new Silex\Application();
    $app['debug'] = true;



    $server = 'mysql:host=127.0.0.1;dbname=hair_salon';
    $username = 'root';
    $password = '';
    $DB = new PDO($server, $username, $password = null);

    $app->register(new Silex\Provider\TwigServiceProvider(), array(
        'twig.path' => __DIR__.'/../views'));

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
        $name = $_POST['name'];
        if (strlen($name) < 1){
            // dummy stylist to send to error page
            $stylist = null;
            // *error page also loads from bad client name input and
            // offers option to reload stylist page
            return $app['twig']->render('error.html.twig', array('stylist' => $stylist));
        }
        $stylist = new Stylist($name);
        $stylist->save();
        return $app['twig']->render('stylist_added.html.twig', array('stylist' => Stylist::find($stylist->getId())));
    });

    // page to edit a stylist
    // route = page displays after user clicks "edit stylist" from that stylist's page
    $app->get('/stylist/{id}/update', function($id) use ($app){
        $stylist = stylist::find($id);
        if ($stylist !== null){
            $stylist = stylist::find($id);
            // $name = $_POST['name'];
            // $stylist->update($name);
            return $app['twig']->render('edit_stylist.html.twig', array('stylist' => $stylist));
        }else{
            return $app['twig']->render('no_client.html.twig');
        }
    });

    //page that renders after an update to stylist, displays that stylists clients
    $app->patch("/stylist/{id}", function($id) use ($app){
        $name = $_POST['name'];
        $stylist = Stylist::find($id);
        $stylist->update($name);
        return $app['twig']->render('clients.html.twig', array('stylist' => $stylist, 'clients' => Client::findByStylistId($id)));
    });

    // DELETE STYLIST PAGE - CONFIRMS DELETE OF STYLIST AND LINK TO HOME PAGE
    $app->delete('/stylist/{id}', function($id) use ($app) {
        $stylist = stylist::find($id);
        // check to make sure there is still a stylist
        if ($stylist !== null){
            // reset Client stylist_id's to null ***
            // $clients = Client::findByStylistId($id);
            // foreach ($clients as $client){
            //     $client->setStylistId(null);
            // }
            // deleteOne deletes clients attached to the stylist. Could change to reassign.
            $stylist->deleteOne($id);
        }
        return $app['twig']->render('index.html.twig', array('stylists' => Stylist::getAll()));
        //, array('stylists' => stylist::getAll()));
    });

    // ADDED CLIENT TO STYLIST RESULT
    $app->post('/stylist_clients', function() use ($app){
        $stylist_id = $_POST['stylist_id'];
        $stylist = Stylist::find($stylist_id);
        // check for valid entry, send stylist info to error page to reload stylist page
        if (strlen($_POST['name']) < 1){
            return $app['twig']->render('error.html.twig', array('stylist' => $stylist));
        }
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
        $client = Client::findByClientId($id);
        $client->setName($name);
        $client->update($name);
        $stylist_id = $client->getStylistId();
        $stylist = Stylist::find($stylist_id);
        return $app['twig']->render('clients.html.twig', array('stylist' => $stylist, 'clients' => Client::findByStylistId($stylist_id)));
    });
    // DELETE CLIENT ROUTE
    $app->delete('/client/{id}', function($id) use ($app) {
        $client = Client::findByClientId($id);
        // check to make sure there is still a client
        if ($client !== null){
            $stylist_id = $client->getStylistId();
            $stylist = Stylist::find($stylist_id);
            $client->delete($id);
            return $app['twig']->render('clients.html.twig', array('stylist' => $stylist, 'clients' => Client::findByStylistId($stylist_id)));
        }else{
            return $app['twig']->render('no_client.html.twig');
        }
        //, array('stylists' => stylist::getAll()));
    });

    return $app;


?>
