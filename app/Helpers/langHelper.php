<?php

use App\Models\Language;

if (! function_exists('__')) {
    function __($key = null, $replace = [], $locale = null)
    {

        if (session()->get('local') == null) {
            $language = Language::where('default_language', 'on')->first();
        }

        $main = $key;
        if (session()->get('local') != null) {
            $path = resource_path()."/lang/".session()->get('local').".json";
            if(!file_exists($path)){
                fopen(resource_path()."/lang/".session()->get('local').".json", "w");
                file_put_contents(resource_path()."/lang/".session()->get('local').".json", '{}');
            }
            $website = json_decode(file_get_contents(resource_path("/lang/".session()->get('local').".json")), true);

            $key = preg_replace('/\s+/S', " ", $key);
            $keyArr = explode(' ', $key);
//            foreach ($keyArr as $word){
//                if(!str_contains($word, '_')){
//                    $strLowerWord = $word;
//                    $modifiedWord = $strLowerWord;
//                    $key = str_replace($word,$modifiedWord,$key);
//                }
//            }

            if (array_key_exists($key, $website)) {
                if (session()->get('local') == null) {
                    return $key;
                }
                return $website[$key];
            }

            $website[$key] = $key;
            file_put_contents(resource_path("/lang/".session()->get('local').".json"), json_encode($website));
            if (session()->get('local') == null) {
                return $key;
            }
        }

        if (is_null($key)) {
            return $key;
        }
        return trans($key, $replace, $locale);
    }
}
