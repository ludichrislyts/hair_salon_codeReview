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
        // check for bad data from a refresh
        if ($stylist !== null){            
            return $app['twig']->render('clients.html.twig', array('stylist' => $stylist, 'clients' => Client::findByStylistId($id)));
        }else{
            return $app['twig']->render('unauthorized_access.html.twig');
        }
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
            return $app['twig']->render('unauthorized_access.html.twig');
        }
    });

    //page that renders after an update to stylist, displays that stylists clients
    $app->patch("/stylist/{id}", function($id) use ($app){
        $stylist = Stylist::find($id);
        if ($stylist !== null){
            $name = $_POST['name'];
            //check to make sure an entry was actually made            
            if (strlen($name) > 0){            
                $stylist->update($name);
                return $app['twig']->render('clients.html.twig', array('stylist' => $stylist, 'clients' => Client::findByStylistId($id)));
            }else{
                return $app['twig']->render('error.html.twig', array('stylist' => $stylist));
            }
        }else{
            return $app['twig']->render('unauthorized_access.html.twig');
        }
    });

    // DELETE STYLIST PAGE - CONFIRMS DELETE OF STYLIST AND LINK TO HOME PAGE
    $app->delete('/stylist/{id}', function($id) use ($app) {
        $stylist = stylist::find($id);
        // check to make sure there is still a stylist
        if ($stylist !== null){
            $stylist->deleteOne($id);
            return $app['twig']->render('index.html.twig', array('stylists' => Stylist::getAll()));
        }else{
            return $app['twig']->render('unauthorized_access.html.twig');
        }
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
    // renders after the edit client page
    $app->patch("/client/{id}", function($id) use ($app){
        $new_name = $_POST['name'];
        $client = Client::findByClientId($id);
        $stylist_id = $client->getStylistId();
        $stylist = Stylist::find($stylist_id);
        //check if somehow page is reached and client is not valid
        if ($client !== null){
            // check for valid entry
            if(strlen($new_name) > 0){
                $client->setName($new_name);
                $client->update($new_name);
                // check if somehow this page is reached and stylist_id is not valid
                if($stylist !== null){
                    return $app['twig']->render('clients.html.twig', array('stylist' => $stylist, 'clients' => Client::findByStylistId($stylist_id)));                
                // not a valid stylist id
                }else{
                    return $app['twig']->render('unauthorized_access.html.twig');
                }
            }else{
                return $app['twig']->render('error.html.twig', array('stylist' => $stylist));
            }
        // not a valid client id
        }else{
            return $app['twig']->render('unauthorized_access.html.twig');
        }
            
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
            return $app['twig']->render('unauthorized_access.html.twig');
        }
        //, array('stylists' => stylist::getAll()));
    });

    return $app;


?>
