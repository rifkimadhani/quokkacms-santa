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
        $filename = self::PATH_TEMPLATE . '/TemplateModel.php';
        $code = self::readFile($filename);

        $modelName = $obj->model->name;
        $table = $obj->model->table;
        $pks = $obj->model->pk;

        $updateFields = $obj->model->updateFields; //di pakai saat operasi insert/update
        $arFieldList = $obj->model->fieldList; //di pakai saat operasi ssp

        $fieldList = '';
        foreach ($arFieldList as $item){
            if (strlen($fieldList)==0) $fieldList = "'{$item}'"; else $fieldList .= ", '{$item}'";
        }

        $pk = ''; //primary key yg pertama, apabila ada lbh dari 1
        $pkWhere = ''; //di pakai utk update & get
        $pkParameter = ''; //di pakai utk parameter saat get dan delete
        $pkCmd = ''; //di pakai pembuatan cmd where saat get dan delete

        $fieldDeclare = '';

        //split pk apabila ada lbh dari 1
        foreach ($pks as $item){

            //convert nama field jadi nama variable
            $name = self::rename($item);

            if (strlen($pk)==0) $pk = $item;
            if (strlen($pkWhere)==0) $pkWhere = "($item=?)"; else $pkWhere .= " AND ($item=?)";
            if (strlen($pkParameter)==0) $pkParameter = "\$$name"; else $pkParameter .= ", \$$name";
            if (strlen($pkCmd)==0) $pkCmd = "->where('{$item}', \${$name})"; else $pkCmd .= "\n            ->where('{$item}', \${$name})";
            if (strlen($fieldDeclare)==0) $fieldDeclare = "\${$name} = \$value['{$item}'];\n"; else $fieldDeclare .= "        \${$name} = \$value['{$item}'];\n";
        }

        foreach ($updateFields as $item){
            $name = self::rename($item);
            if (in_array($item, $pks)==false){
                //filter out apabila ada fields pk
                if (strlen($fieldDeclare)==0) $fieldDeclare = "\${$name} = \$value['{$item}'];\n"; else $fieldDeclare .= "        \${$name} = \$value['{$item}'];\n";
            }
        }

        $allowedFields = self::genAllowedFields($updateFields);
        $sqlUpdateFields = self::genUpdateFields($pks, $updateFields);
        $modifyFields = self::genModifyFields($pks, $updateFields);

        //lakukan replace di sini
        $code = str_replace('__TODAY__', self::today(), $code);
        $code = str_replace('__Model__', $modelName, $code);
        $code = str_replace('__table__', $table, $code);

        $code = str_replace('__pk__', $pk, $code);
        $code = str_replace('__pk_where__', $pkWhere, $code);

        $code = str_replace('__sql_update_fields__', $sqlUpdateFields, $code);
        $code = str_replace('__allowedFields__', $allowedFields, $code);
        $code = str_replace('$__pk_parameter__', $pkParameter, $code);
        $code = str_replace('$__pk_field__', $pkParameter, $code);
        $code = str_replace('//__get_cmd__', $pkCmd, $code);

        $code = str_replace('//__field_declare__', $fieldDeclare, $code);
        $code = str_replace('__fieldList__', $fieldList, $code);
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
        $filename = self::PATH_TEMPLATE . '/TemplateForm.php';
        $code = self::readFile($filename);

        $formName= $obj->form->name;
        $fields = $obj->form->fields;

        //build member & attribute
        $member = '';
        $attr = '';
        foreach ($fields as $item){
            $name = $item->field;
            $type = $item->type;
            $req = $item->required;

            $member .= "    public \${$name};\n";

            if ($type=='hidden'){
                //type=hidden tdk perlu title dan placeholder
                $attr .= "        \$this->{$name} = ['type'=>'{$type}'];\n";
            } else {
                $attr .= "        \$this->{$name} = ['type'=>'{$type}', 'label'=>'{$name}', 'placeholder'=>'', 'required'=>'{$req}'];\n";
            }
        }

        //write to code
        $code = str_replace('__TODAY__', self::today(), $code);
        $code = str_replace('__Form__', $formName, $code);
        $code = str_replace('//__member__', $member, $code);
        $code = str_replace('//__attr__', $attr, $code);


        //write code yg sdh di rubah ke folder output
        $output = fopen("$pathOut/{$formName}.php", "w");
        fwrite($output, $code);
        fclose($output);
    }

    static public function buildView($obj){

        $pathOut = self::PATH_OUTPUT . '/' . $obj->folder;
        self::mkdir($pathOut);

        //read template model file
        $filename = self::PATH_TEMPLATE . '/template-index.php';
        $code = self::readFile($filename);

        $viewName = $obj->view->name;
        $title = $obj->view->dialogTitle;
        $formName = $obj->form->name;

        //field2 yg muncul pada datatable
        $fieldList = $obj->model->fieldList;
        $pks = $obj->model->pk;

        $pkValue = '';
        $urlEdit = '';

        //build cmd utk ambil pk dari data
        foreach ($pks as $pk){
            //cari field pk dari daftar fields
            $idx = self::findField($pk, $fieldList);

            $name = self::rename($pk);
            $pkValue .= "            const {$name} = data[{$idx}];\n";

            if (strlen($urlEdit)==0){
                $urlEdit = "{$name}";
            } else {
                $urlEdit .= " + '/' + {$name}";
            }
        }

        //write to code
        $code = str_replace('__TODAY__', self::today(), $code);
        $code = str_replace('__Form__', $formName, $code);
        $code = str_replace('__TITLE__', $title, $code);

        $code = str_replace('__pk_value__', $pkValue, $code);
        $code = str_replace('__url_edit__', $urlEdit, $code);


        //write code yg sdh di rubah ke folder output
        $output = fopen("$pathOut/{$viewName}.php", "w");
        fwrite($output, $code);
        fclose($output);
    }

    static public function buildController($obj){

        $pathOut = self::PATH_OUTPUT . '/' . $obj->folder;
        self::mkdir($pathOut);

        //read template model file
        $filename = self::PATH_TEMPLATE . '/Template.php';
        $code = self::readFile($filename);

        $controllerName = $obj->controller->name;
        $title = $obj->controller->title;

        $modelName = $obj->model->name;
        $pks = $obj->model->pk;

        $formName = $obj->form->name;
//        $fields = $obj->form->fields;

        $viewName = $obj->view->name;
        $viewFolder = $obj->view->folder;
        $view = "$viewFolder/$viewName";

        //
//        $updateField  = '';
//        $modifyField = '';

        $pkParameter = '';
        foreach ($pks as $pk){
            $name = self::rename($pk);
            if (strlen($pkParameter)==0) $pkParameter = "\$$name"; else $pkParameter .= ", \$$name";
        }

        //write to code
        $code = str_replace('__TODAY__', self::today(), $code);
        $code = str_replace('__Controller__', $controllerName, $code);
        $code = str_replace('__view__', $view, $code);
        $code = str_replace('__title__', $title, $code);
        $code = str_replace('__Model__', $modelName, $code);
        $code = str_replace('__Form__', $formName, $code);
        $code = str_replace('__pk_param__', $pkParameter, $code);

//        $code = str_replace('__update_field__;', $updateField, $code);
//        $code = str_replace('__modify_field__', $modifyField, $code);


        //write code yg sdh di rubah ke folder output
        $output = fopen("$pathOut/{$controllerName}.php", "w");
        fwrite($output, $code);
        fclose($output);
    }

    static public function buildRoute($obj){

        $pathOut = self::PATH_OUTPUT . '/' . $obj->folder;
        self::mkdir($pathOut);

        $name = $obj->controller->name;
        $pks = $obj->model->pk;
        $nameLower = strtolower($name);
        $date = self::today();

        $urlParameter = '';
        $urlFunction = '';

        $idx = 0;
        //build cmd utk ambil pk dari data
        foreach ($pks as $pk){
            $idx++;
            if (strlen($urlParameter)==0) $urlParameter = '(:any)'; else $urlParameter .= '/(:any)';
            if (strlen($urlFunction)==0) $urlFunction = "\${$idx}"; else $urlFunction .= "/\${$idx}";
        }

        //
        $output = <<<PHP
<?php
/**
 * Created by PageBuilder
 * Date: $date
 */
 
    \$routes->get('/$nameLower', '$name::index');
    \$routes->get('/$nameLower/ssp', '$name::ssp');
    \$routes->get('/$nameLower/edit/{$urlParameter}', '$name::edit/{$urlFunction}');
    \$routes->get('/$nameLower/delete/{$urlParameter}', '$name::delete/{$urlFunction}');
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

    static public function readFile($filename){
        $file = fopen($filename, "r");
        $code = fread($file, filesize($filename));
        fclose($file);
        return $code;
    }

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
    static protected function genUpdateFields($pks, $fields){
        $output = '';
        foreach ($fields as $item){
            //pk tdk boleh include dalam update
            if (in_array($item, $pks)) continue;
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
    static protected function genModifyFields($pks, $fields){
        $output = '';
        foreach ($fields as $item){
            //untuk modify pk (primaru key), tdk boleh di include
            //krn akan error di sql nya
            if (in_array($item, $pks)==false){
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

    /**
     * cari field dari daftar field
     * @param $needle
     * @param $list
     */
    static protected function findField($needle, $list){
        $idx = 0;
        foreach ($list as $item){
            if ($item==$needle) {
//                $item->idx = $idx; //tambahkan index pada item
                return $idx;
            }
            $idx++;
        }
        return null;
    }
}