<?php
    /**
    * @backupGlobals disabled
    * @backupStaticAttributes disabled
    */
    require_once "src/Stylist.php";
    // require_once "src/Restaurant.php";
    $server = 'mysql:host=localhost;dbname=hair_salon_test';
    $username = 'root';
    $password = 'root';
    $DB = new PDO($server, $username, $password);

    class StylistTest extends PHPUnit_Framework_TestCase
    {
        // protected function tearDown()
        // {
        //     Stylist::deleteAll();
        // }

        // function test_save()
        // {
        //     //Arrange
        //     $name = "Chris";
        //     $test_stylist = new Stylist($name);
        //     //Act
        //     $test_stylist->save();
        //     //var_dump($test_stylist);
        //     $result = Stylist::getAll();
        //     //Assert
        //     $this->assertEquals($test_stylist, $result[0]);
        // }

        function test_getAll()
        {
            //Arrange
            $name1 = "Chris";
            $name2 = "Mary";
            $test_stylist1 = new Stylist($name1);
            $test_stylist1->save();
            $test_stylist2 = new Stylist($name2);
            $test_stylist2->save();
            //Act
            $result = Stylist::getAll();
            var_dump($result);
            //Assert
            $this->assertEquals([$test_stylist1, $test_stylist2], $result);
        }


    }
?>
