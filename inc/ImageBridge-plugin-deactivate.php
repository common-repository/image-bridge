<?php 

class ImageBridgePluginDeactivate
{
    public static function deactivate(){
        flush_rewrite_rules();
    }
}