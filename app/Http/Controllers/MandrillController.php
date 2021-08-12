<?php
/**
 * Created by PhpStorm.
 * User: gil
 * Date: 5/4/18
 * Time: 7:31 PM
 */

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Config;

class MandrillController extends Controller
{
    private $mandrill;

    public function __construct(){
        $this->mandrill = new \Mandrill(Config::get('mandrillP2B.api_key'));
    }

    /**
     * Get template by name of template
     * @param $label
     * @return \struct
     */
    public function getTemplateByLabel($label){
        return $this->mandrill->templates->info($label);
    }

    /**
     * Render variables in template
     * @param $label
     * @param $variables
     * @return \struct
     */
    public function renderTemplate($label, $variables){
        $content = $this->getTemplateByLabel($label);
        $template_name = $label;
        $merge = [];
        foreach ($variables as $key => $value){
            $merge[] = ['name' => $key, 'content' => $value];
        }
        $template_content = array(
            array(
                'content' => $content['publish_code'],
            )
        );
        return $this->mandrill->templates->render($template_name, $template_content, $merge);
    }
}