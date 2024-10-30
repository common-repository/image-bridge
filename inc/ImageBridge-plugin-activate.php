<?php 

class ImageBridgePluginActivate
{
    public static function activate(){
        flush_rewrite_rules();
    }
}

