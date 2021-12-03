# Zote
==================
## Create Project
```
php zote-framework/zote init 
```
After init prject, file structure will be
```
 [dir] zote-framework
    []...
 [dir] project
    [file] .env
    [dir] public
        [file] index.php
        [file] .htaccess
        [file] robots.txt
    [dir] mobile
        [file] User.php
    [dir] migration
        [file] createUser.php
    [dir] controller
        [file] userController.php
    [file] route.php
```

- - -

## Route
 *  without controller
 *  with controller
 *  route prefix
 *  route Authorization {with guard}
### Route Example
 *  Route : without controller
```php
 <?php
/* route without controller*/
$route->addroute('post','/','',function(){
    echo "hello";
});
$route->addroute('post','/direct','',function(){
    echo "hello direct";
});
?>
```
 *   Route : controller
```php
 <?php
/* 
route with controller and function
controller : UserController
function : testFun
*/
$route->addroute('get','/test','UserController','testFun');
?>
```

 *   Route : Prefix
```php
<?php
/* 
route prefix
*/
$route->routePrefix("/demo",function($route){
    /* /demo/d1 */
    $route->addroute('get','/d1','DemoController','d1Fun');
    /* /demo/d2 */
    $route->addroute('get','/d2','DemoController','d2Fun');

    //nested route prefix
    $route->routePrefix("/dx",function($route){
         /* /demo/dx/dx1 */
        $route->addroute('get','/dx1','DemoController','dx1Fun');
    });
    $route->routePrefix("/dy",function($route){
        /* /demo/dy/dy1 */
        $route->addroute('get','/dy1','DemoController','dy1Fun');
    });
});
?>
```
- - -
## Model
 *  Create Model
 *  Insert
 *  Update
 *  Delete
 *  Select , Where , Group , Order

### Model Example

 *  Create Model

```php
<?php
namespace Model;
use zFramework\Schema\Table;

class Item extends Table{
    public static $tableName="Item";

    protected static $columnName=['itemId',"name"];

    protected static $primaryKeys = ['itemId'];

    protected static $autoIncreaseKeys = ['itemId'];

    protected static $hiddenColumns = [];

    protected $softDelete = false;

    function __construct() {
        parent::__construct();
    }
    

}
?>
```
 *  Insert

```php
 <?php
namespace Controller;
use zFramework\providers\Request;
use Model\Item;
class ItemController{

    public function insertFun(Request $request){
        $item = new Item();
        $item->itemId = $request->get('id');
        $item->name = "Item One";
        $item->save();
    }
}
?>
```
 *  Update

```php
 <?php
namespace Controller;
use zFramework\providers\Request;
use Model\Item;
class ItemController{

    public function updateFun(Request $request){
        /*
        you can update in two ways
        */
        /* first way */
        $item = Item::find($request->get('id'));
        $item->name = "ok";
        $item->update();
        /* OR */
        /* second way */
        $item = new Item();
        $item->itemId = $request->get('id');
        $item->name = "Item One";
        $item->update();
        return $item;
    }
}
?>
```
 *  Select , Where , Group , Order

```php
 <?php
namespace Controller;
use zFramework\providers\Request;
use Model\Item;
class ItemController{

    public function selectFun(Request $request){
        $item = Item::select("id",'name')->get();
        //where
        $item = Item::select("id",'name')->where("id in (1,2)")->get();
        //order by
        $item = Item::select("id",'name')->where("id in (1,2)")->orderBy("id desc")->get();
        //group by
        $item = Item::select("SUM(qty) as quantity",'name')->where("id in (1,2,3,4)")->orderBy("name desc")->groupBy("name")->get();

        //getAll
        $users = User::select("id","name")->getAll();
        foreach($users as $user){
            echo "name : ". $user['name'] .'<br>';
        }

        //paginate
        $users = User::select("id","name")
                    ->paginate($page_at-1,$row_count);
        

            $data = array(
                "code" => 200,
                "status" => "success",
                "message" => "success",
                "pagination" => $users->paginate,
                "data" => $users->data
            );
            return $data;

    }
}
?>
```
 - - -

## Validation
 *  rule
 *  single file and multi files validation rule

### Validation Example
 *  rule

```php
 <?php
namespace Controller;
use zFramework\providers\Request;
use zFramework\providers\Validator;
class ItemController{

 public function validationFun(Request $request){
        $validator = Validator::Rule(function($validator){
            $validator->field("name")->max(4,"the name should be under 200 length");
            $validator->field("name")->min(2);
            $validator->field("address")->min(5)->notNull();
            //custom validation rule
            $validator->field("address")->custom(function($validator) use ( $request){
                if($request->get('address')!="Sanchaung"){
                    $validator->setError("Custom validation erro");
                }
            });
        });

        $v = $validator->validate();

        if(!$v){
            $data = array(
                "status_code" => 400,
                "status" => "failed",
                "validate"=>$v,
                "errors" => $validator->error()
            );
            return $data;
        }
    }
}
?>
```



 *  single file and multi files validation rule

```php
 <?php
namespace Controller;
use zFramework\providers\Request;
use zFramework\providers\Validator;
class ItemController{

 public function validationFun(Request $request){
       $validator = Validator::Rule(function($validator){
            //single files
            $validator->file("profile_image")
                ->extenstions("jpg","png")
                ->minetype("image/jpeg","image/png");

            //multi files
            $validator->files("image")
                ->extenstions("jpg","png")
                ->minetype("image/jpeg","image/png");
        });

        $v = $validator->validate();

        if(!$v){
            $data = array(
                "status_code" => 400,
                "status" => "failed",
                "validate"=>$v,
                "errors" => $validator->error()
            );
            return $data;
        }
    }
}
?>
```

 - - -