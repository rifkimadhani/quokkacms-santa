<?php
/**
 * Created by PhpStorm.
 * User: erick
 * Date: 2/21/2023
 * Time: 9:44 AM
 */

namespace App\Libraries;

class PageBuilder
{
    const PATH_TEMPLATE = __DIR__ . '/template';
    const PATH_OUTPUT = __DIR__ . '/../../writable/output'; //path ini harus bisa di write

    /**
     * @param $json
     */
    static public function buildAll($json){
        self::buildModel($json);
        self::buildForm($json);
        self::buildView($json);
        self::buildController($json);
        self::buildRoute($json);
    }

    static public function buildModel($obj){

        $pathOut = self::PATH_OUTPUT . '/' . $obj->folder;
        self::mkdir($pathOut);

        //read template model file
        $filename = self::PATH_TEMPLATE . '/SampleModel.php';
        $file = fopen($filename, "r");
        $code = fread($file, filesize($filename));
        fclose($file);

        $modelName = $obj->model->name;
        $table = $obj->model->table;
        $pk = $obj->model->pk;
        $updateFields = $obj->model->updateFields; //di pakai saat operasi insert/update
        $fieldList = $obj->model->fieldList; //di pakai saat operasi ssp

        $allowedFields = self::genAllowedFields($updateFields);
        $sqlUpdateFields = self::genUpdateFields($pk, $updateFields);
        $modifyFields = self::genModifyFields($pk, $updateFields);

        //lakukan replace di sini
        $code = str_replace('__TODAY__', self::today(), $code);
        $code = str_replace('__Model__', $modelName, $code);
        $code = str_replace('__table__', $table, $code);
        $code = str_replace('__pk__', $pk, $code);
        $code = str_replace('__allowedFields__', $allowedFields, $code);
        $code = str_replace('__fieldList__', $fieldList, $code);

        $code = str_replace('__sql_update_fields__', $sqlUpdateFields, $code);
        $code = str_replace('$__modify_fields__', $modifyFields, $code);

        //write code yg sdh di rubah ke folder output
        $output = fopen( "$pathOut/{$modelName}.php", "w");
        fwrite($output, $code);
        fclose($output);
    }

    static public function buildForm($obj){

        $pathOut = self::PATH_OUTPUT . '/' . $obj->folder;
        self::mkdir($pathOut);

        //read template model file
        $filename = self::PATH_TEMPLATE . '/SampleForm.php';
        $file = fopen($filename, "r");
        $code = fread($file, filesize($filename));
        fclose($file);

        $formName= $obj->form->name;
        $fields = $obj->form->fields;

        //build member & attribute
        $member = '';
        $attr = '';
        foreach ($fields as $item){
            $name = $item->name;
            $type = $item->type;

            $member .= "    public \${$name};\n";

            if ($type=='hidden'){
                //type=hidden tdk perlu title dan placeholder
                $attr .= "        \$this->{$name} = ['type'=>'{$type}'];\n";
            } else {
                $attr .= "        \$this->{$name} = ['type'=>'{$type}', 'label'=>'{$name}', 'placeholder'=>'', 'required'=>'required'];\n";
            }
        }

        //write to code
        $code = str_replace('__TODAY__', self::today(), $code);
        $code = str_replace('__Form__', $formName, $code);
        $code = str_replace('__member__', $member, $code);
        $code = str_replace('__attr__', $attr, $code);


        //write code yg sdh di rubah ke folder output
        $output = fopen("$pathOut/{$formName}.php", "w");
        fwrite($output, $code);
        fclose($output);
    }

    static public function buildView($obj){

        $pathOut = self::PATH_OUTPUT . '/' . $obj->folder;
        self::mkdir($pathOut);

        //read template model file
        $filename = self::PATH_TEMPLATE . '/sample-index.php';
        $file = fopen($filename, "r");
        $code = fread($file, filesize($filename));
        fclose($file);

        $viewName = $obj->view->name;
        $title = $obj->view->dialogTitle;
        $formName = $obj->form->name;

        //build member & attribute


        //write to code
        $code = str_replace('__TODAY__', self::today(), $code);
        $code = str_replace('__Form__', $formName, $code);
        $code = str_replace('__TITLE__', $title, $code);


        //write code yg sdh di rubah ke folder output
        $output = fopen("$pathOut/{$viewName}.php", "w");
        fwrite($output, $code);
        fclose($output);
    }

    static public function buildController($obj){

        $pathOut = self::PATH_OUTPUT . '/' . $obj->folder;
        self::mkdir($pathOut);

        //read template model file
        $filename = self::PATH_TEMPLATE . '/Sample.php';
        $file = fopen($filename, "r");
        $code = fread($file, filesize($filename));
        fclose($file);

        $controllerName = $obj->controller->name;
        $title = $obj->controller->title;

        $modelName = $obj->model->name;
        $pk = $obj->model->pk;

        $formName = $obj->form->name;
        $fields = $obj->form->fields;

        $viewName = $obj->view->name;
        $viewFolder = $obj->view->folder;
        $view = "$viewFolder/$viewName";

        //
        $updateField  = '';
        $modifyField = '';
        foreach ($fields as $item){
            $name = $item->name;
            $varname = self::rename($item->name);
            $type = $item->type;

            //hanya render, apabila ini bukan pk
            if ($name!=$pk) {
                $updateField .= "        \${$varname} = \$_POST['{$name}'];\n";
                if (strlen($modifyField)==0) $modifyField = "\${$varname}"; else $modifyField .= ", ${$varname}";
            }
        }


        //write to code
        $code = str_replace('__TODAY__', self::today(), $code);
        $code = str_replace('__Controller__', $controllerName, $code);
        $code = str_replace('__view__', $view, $code);
        $code = str_replace('__title__', $title, $code);
        $code = str_replace('__Model__', $modelName, $code);
        $code = str_replace('__Form__', $formName, $code);
        $code = str_replace('__pk__', $pk, $code);

        $code = str_replace('__update_field__;', $updateField, $code);
        $code = str_replace('__modify_field__', $modifyField, $code);


        //write code yg sdh di rubah ke folder output
        $output = fopen("$pathOut/{$controllerName}.php", "w");
        fwrite($output, $code);
        fclose($output);
    }

    static public function buildRoute($obj){

        $pathOut = self::PATH_OUTPUT . '/' . $obj->folder;
        self::mkdir($pathOut);

        $name = $obj->controller->name;
        $nameLower = strtolower($name);
        $date = self::today();

        //
        $output = <<<PHP
<?php
/**
 * Created by PageBuilder
 * Date: $date
 */
 
    \$routes->get('/$nameLower', '$name::index');
    \$routes->get('/$nameLower/ssp', '$name::ssp');
    \$routes->get('/$nameLower/edit/(:num)', '$name::edit/$1');
    \$routes->get('/$nameLower/delete/(:num)', '$name::delete/$1');
    \$routes->post('/$nameLower/update', '$name::update');
    \$routes->post('/$nameLower/insert', '$name::insert');
?>
PHP;

        //write code yg sdh di rubah ke folder output
        $file = fopen("$pathOut/Routes_{$name}.php", "w");
        fwrite($file, $output);
        fclose($file);
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    static protected function mkdir($path){
        if (!file_exists($path)) {
            mkdir($path);
        }
    }

    /**
     * return todays date
     *
     * @return false|string
     */
    static protected function today(){
        return date("Y-m-d H:i:s");
    }

    static protected function genAllowedFields($fields){
        $output = '';
        foreach ($fields as $item){
            if (strlen($output)>0) $output .= ", '{$item}'"; else $output .= "'{$item}'";
        }
        return $output;
    }

    /**
     * Generate list utk SQL update
     *
     * @param $pk string primaryKey akan di filter dari fields
     * @param $fields array, daftar column yg boleh di update, field pk otomatis di exclude
     * @return string
     */
    static protected function genUpdateFields($pk, $fields){
        $output = '';
        foreach ($fields as $item){
            //pk tdk boleh include dalam update
            if ($item==$pk) continue;
            if (strlen($output)>0) $output .= ", {$item}=?"; else $output .= "{$item}=?";
        }
        return $output;
    }

    /**
     * Di pakai saat pembuatan function modify
     *
     * @param $pk string, col primaryKey sdh di sediakan khsus, jadi tdk perlu di masukan lagi
     * @param $fields array
     * @return string
     */
    static protected function genModifyFields($pk, $fields){
        $output = '';
        foreach ($fields as $item){
            //untuk modify pk (primaru key), tdk boleh di include
            //krn akan error di sql nya
            if ($item!=$pk){
                $item = self::rename($item);
                if (strlen($output)>0) $output .= ", \${$item}"; else $output .= "\${$item}";
            }
        }
        return $output;
    }

    /**
     * melakukan penamaan camelcase dari xxx_yyy --> xxxYyy
     * nama yg di pisahkan dgn _ otomatis di rubah
     *
     * @param $string
     * @param bool $capitalizeFirstCharacter
     * @return mixed
     */
    static protected function rename($string, $capitalizeFirstCharacter = false)
    {
        $str = str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));

        if (!$capitalizeFirstCharacter) {
            $str[0] = strtolower($str[0]);
        }

        return $str;
    }
}