<?php
/**
 * Plugin class
 */

namespace Phile\Plugin\Phile\TwigFilters;

use Phile\Core\ServiceLocator;
use Phile\Event\CoreEvent;
use Phile\Plugin\AbstractPlugin;
use Phile\Plugin\TemplateTwig\TwigTemplatePlugin;

/**
 * An example plugin showing how to make Twig filters
 * usage {{ content|excerpt }}
 */
class Plugin extends AbstractPlugin {
    public static function getSubscribedEvents(){
        return [
            CoreEvent::LOADED => 'onCoreLoaded'
        ];
    }

    public function onCoreLoaded(){
        $template = ServiceLocator::getService('Phile_Template');
        if ($template instanceof TwigTemplatePlugin){
            $twig = $template->getTwig();

            // grab the first paragraph and remove all the html code
            $excerpt = new \Twig_SimpleFilter('excerpt', function ($string){
                return strip_tags(substr($string, 0, strpos($string, "</p>") + 4));
            });
            $twig->addFilter($excerpt);

            // limit words function -- very rough limit due to HTML input
            $limit_words = new \Twig_SimpleFilter('limit_words', function ($string){
                $words = str_word_count($string, 2);
                $nbwords = count($words);
                $pos = array_keys($words);
                if ($this->config['limit'] >= $nbwords){
                    return trim($string);
                } else {
                    return trim(substr($string, 0, $pos[$this->config['limit']])) . '...';
                }
            });
            $twig->addFilter($limit_words);
        }
    }
}
