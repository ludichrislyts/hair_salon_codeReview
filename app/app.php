require_once __DIR__."/../vendor/autoload.php";
require_once __DIR__."/../src/Restaurant.php";
require_once __DIR__."/../src/Cuisine.php";


$app = new Silex\Application();
$app['debug'] = true;


$server = 'mysql:host=localhost;dbname=restaurants';
$username = 'root';
$password = 'root';
$DB = new PDO($server, $username, $password);


$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views'
));

use Symfony\Component\HttpFoundation\Request;
Request::enableHttpMethodParameterOverride();

//root page: loads into index.html.twig
//options on page to goto get->/restaurants or get->/cuisine
$app->get("/", function() use ($app) {
    return $app['twig']->render('index.html.twig');
});

//Lists all restaurants
//comes from index.html.twig
//goes to restaurants.html.twig
//option on page to goto post->/restaurants or post->/delete_restaurants
$app->get("/restaurants", function() use ($app) {
    return $app['twig']->render('restaurants.html.twig', array('restaurants' => Restaurant::getAll()));
});

//Adds a restaurant
//comes from restaurants.html.twig
//goes to restaurants.html.twig
//option on page to goto post->/restaurants or post->/delete_restaurants
$app->post("/restaurants", function() use ($app) {
    $restaurant = new Restaurant($_POST['restaurant_name']);
    $restaurant->save();
    return $app['twig']->render('restaurants.html.twig', array('restaurants' => Restaurant::getAll()));
});

//Deletes all restaurants
//comes from restaurants.html.twig
//goes to index.html.twig
//options on page to goto get->/restaurants or get->/cuisine
$app->post("/delete_restaurants", function() use ($app) {
    Restaurant::deleteAll();
    return $app['twig']->render('index.html.twig');
});

//Allows user to delete or update a specific restaurant
//comes from restaurants.edit.html.twig
//option on page to goto post->/restaurants/{id} (with either patch or delete)
$app->get("/restaurants/{id}/edit", function($id) use($app) {
    $restaurant = Restaurant::find($id);
    return $app['twig']->render('restaurant_edit.html.twig', array('restaurant' => $restaurant));
});

//Goes to this page after user hits 'update restaurant'
//comes from get->/restaurants/{id}/edit
//goes to restaurants.html.twig
//option on page to goto post->/restaurants or post->/delete_restaurants
$app->patch("/restaurants/{id}", function($id) use($app) {
    $restaurant_name = $_POST['restaurant_name'];
    $restaurant = Restaurant::find($id);
    $restaurant->update($restaurant_name);
    $restaurants = Restaurant::getAll();
    return $app['twig']->render('restaurants.html.twig', array('restaurants' => $restaurants));
});

//Goes to this page after user hits 'delete restaurant'
//comes from get->/restaurants/{id}/edit
//goes to restaurants.html.twig
//option on page to goto post->/restaurants or post->/delete_restaurants
$app->delete("/restaurants/{id}", function($id) use ($app) {
    $restaurant = Restaurant::find($id);
    $restaurant->deleteOne();
    return $app['twig']->render('restaurants.html.twig', array('restaurants' => Restaurant::getAll()));
});




//Lists all cuisines and restaurants associated with the cuisines
//Comes from index.html.twig
//goes to post->/cuisine or post->/delete_cuisines or /cuisine/{id}/edit
$app->get("/cuisine", function() use ($app) {
    return $app['twig']->render('cuisine.html.twig', array('cuisine' => Cuisine::getAll(), 'restaurants' => Restaurant::getAll()));
});

//Adding a new cuisine instance
//Comes from self, renders to self
$app->post("/cuisine", function () use ($app) {
    $id = null;
    $restaurant_id = intval($_POST['restaurant_id']);
    $restaurant_name = Restaurant::find($restaurant_id);
    $cuisine = new Cuisine($_POST['cuisine_name'], $id, $restaurant_id);
    $cuisine->save();
    return $app['twig']->render('cuisine.html.twig', array('cuisine' => Cuisine::getAll(), 'restaurants' => Restaurant::getAll()));
});

//Deletes all cuisines
//Comes from cuisine.html.twig
//Goes to index.html.twig
$app->post("/delete_cuisine", function() use ($app) {
    Cuisine::deleteAll();
    return $app['twig']->render('index.html.twig');
});

//Find and return a cuisine to edit
//Comes from cuisine.html.twig
//Goes to cuisine_edit.html.twig to allow for deleting or updating
$app->get("/cuisine/{id}/edit", function($id) use($app) {
    $cuisine = Cuisine::find($id);
    return $app['twig']->render('cuisine_edit.html.twig', array('cuisine' => $cuisine));
});

//Update cuisine name
//Comes from cuisine_edit with passed in cuisine ID
//Render cuisine with the new updated cuisine
//post->/cuisine
$app->patch("/cuisine/{id}", function($id) use($app) {
    $cuisine_name = $_POST['cuisine_name'];
    $cuisine = Cuisine::find($id);
    $cuisine->update($cuisine_name);
    $cuisines = Cuisine::getAll();
    return $app['twig']->render('cuisine.html.twig', array('cuisine' => $cuisines, 'restaurants' => Restaurant::getAll()));
});

//Delete one cuisine
//Comes from cuisine_edit with passed in cuisine ID
//Render cuisine with missing cuisine
//post-/cuisine
$app->delete("/cuisine/{id}", function($id) use ($app) {
    $cuisine = Cuisine::find($id);
    $cuisine->deleteOne();
    return $app['twig']->render('index.html.twig', array('cuisine' => Cuisine::getAll()));
});


return $app;

?>
