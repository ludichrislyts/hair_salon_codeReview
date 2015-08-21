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
        protected function tearDown()
        {
            Stylist::deleteAll();
        }

        function test_Save()
        {
            //Arrange
            $name = "Chris";
            $test_stylist = new Stylist($name);
            //Act
            $test_stylist->save();
            //var_dump($test_stylist);
            $result = Stylist::getAll();
            //Assert
            $this->assertEquals($test_stylist, $result[0]);
        }

        function test_GetAll()
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
            //Assert
            $this->assertEquals([$test_stylist1, $test_stylist2], $result);
        }

        function test_DeleteAll()
        {
            //Arrange
            $name1 = "Chris";
            $name2 = "Mary";
            $test_stylist1 = new stylist($name1);
            $test_stylist1->save();
            $test_stylist2 = new stylist($name2);
            $test_stylist2->save();
            //Act
            stylist::deleteAll();
            //Assert
            $result = stylist::getAll();
            $this->assertEquals([], $result);
        }

        function test_GetId()
        {
            //Arrange
            $name = "Chris";
            $test_stylist = new Stylist($name);
            $test_stylist->save();
            //Act
            $result = $test_stylist->getId();
            //Assert
            $this->assertEquals(true, is_numeric($result));
        }

        function test_GetName()
        {
            //Arrange
            $name = "Chris";
            $test_stylist = new Stylist($name);
            $test_stylist->save();

            //Act
            $result = $test_stylist->getName();
            var_dump($result);

            //Assert
            $this->assertEquals("Chris", $result);
        }

        function test_Find()
        {
            //Arrange
            $name1 = "Chris";
            $test_stylist1 = new stylist($name1);
            $test_stylist1->save();
            $name2 = "Sarah";
            $test_stylist2 = new stylist($name2);
            $test_stylist2->save();
            //Act
            $result = stylist::find($test_stylist1->getId());
            //Assert
            $this->assertEquals($test_stylist1, $result);
        }

        function test_Update()
        {
            //Arrange
            $name = "Chris";
            $test_stylist = new stylist($name);
            $test_stylist->save();
            $new_name = "Chris Lytsell";
            //Act
            $test_stylist->update($new_name);
            $result = $test_stylist->getname();
            //Assert
            $this->assertEquals($new_name, $result);
        }

        function test_deleteOne()
        {
           //Arrange
           $name1 = "Chris";
           $test_stylist1 = new stylist($name1);
           $test_stylist1->save();
           $name2 = "Sarah";
           $test_stylist2 = new stylist($name2);
           $test_stylist2->save();
           //Act
           $test_stylist1->deleteOne();
           $result = stylist::getAll();
           //Assert
           $this->assertEquals([$test_stylist2], $result);
        }




    }
?>
