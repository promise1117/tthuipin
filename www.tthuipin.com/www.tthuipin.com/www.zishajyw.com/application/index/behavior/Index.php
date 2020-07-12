<?php
namespace app\index\behavior;
class Index{
    public function run($params){
        echo '这是个行为'.$params;
    }
}