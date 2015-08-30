<?php

namespace samson\tags;

use samson\activerecord\dbRecord;
use samson\core\CompressableService;

/**
 * Show mata links for social networks
 * For use this module need create structure and write its id to structureId in config this module
 * Then create depend material and field for store data in this structure
 * And use this <?php m('tags')->render('')?> for insert tags into head tag
 * @author Molodyko Ruslan <molodyko@samsonos.com>
 * @copyright 2015 SamsonOS
 * @version 0.1
 */
class Core extends CompressableService
{
    /** Module identifier */
    public $id = 'tags';

    /** @var int Structure id */
    public $structureId;

    /** Name of column title in structure */
    public $title = 'Соц_Сеть_Название';

    /** Name of column description in structure */
    public $description = 'Соц_Сеть_Описание';

    /** Name of column path to image in structure */
    public $image = 'Соц_Сеть_Картинка';

    /** Name of column site name in structure */
    public $siteName = 'Соц_Сеть_Название_Сайта';

    /** Name of column author in structure */
    public $author = 'Соц_Сеть_Автор';

    /**
     * Render tags
     */
    public function __handler()
    {
        // Get url of item
        $url = $this->getItemUrl();

        // Get data for meta tags
        $data = $this->getData($url);

        // Render result
        $this->view('tags')->set($data);
    }

    /**
     * Get data
     * @param $url String with url current page
     * @return array Title, description and path to image
     */
    // TODO remove dbQuery dependency
    public function getData($url)
    {
        // Get common data
        $result = $this->getCommonData();

        $material = null;
        // If exists material with such url and this material is belong to right structure then use it
        if (dbQuery('samson\cms\CMSMaterial')->Url($url)->first($material)) {
            if (dbQuery('samson\cms\CMSNavMaterial')->StructureID($this->structureId)->cond('MaterialID',
                    $material->MaterialID)->first() !== null
            ) {

                // If data in material exists and not empty then redefine common values
                if (isset($material[$this->title]) && !empty($material[$this->title])) {
                    $result['title'] = $material[$this->title];
                }
                if (isset($material[$this->description]) && !empty($material[$this->description])) {
                    $result['description'] = $material[$this->description];
                }
                if (isset($material[$this->image]) && !empty($material[$this->image])) {
                    $result['image'] = $material[$this->image];
                }
                if (isset($material[$this->author]) && !empty($material[$this->author])) {
                    $result['author'] = $material[$this->author];
                }
                if (isset($material[$this->siteName]) && !empty($material[$this->siteName])) {
                    $result['site_name'] = $material[$this->siteName];
                }
            }
        }

        // Get result
        return $result;
    }

    /**
     * Get common data for all not defined pages
     * @return array title, description and path to image
     */
    public function getCommonData()
    {
        // Get structure
        $structure = dbQuery('structure')->StructureID($this->structureId)->first();

        // Get material
        $material = dbQuery('samson\cms\CMSMaterial')->MaterialID($structure->MaterialID)->first();

        // Get common result
        return array(
            'title' => $material[$this->title],
            'description' => $material[$this->description],
            'image' => $material[$this->image],
            'site_name' => $material[$this->siteName],
            'author' => $material[$this->author]
        );

    }

    /**
     * Get expected url of material
     * @return string last item of uri
     */
    public function getItemUrl()
    {
        // Get url
        $url = url()->text();

        // Get last part of url
        if (strpos($url, '/') !== false) {
            $arr = explode('/', $url);
            $path = array_pop($arr);
        } else {
            $path = $url;
        }

        // Get path
        return $path;
    }
}
