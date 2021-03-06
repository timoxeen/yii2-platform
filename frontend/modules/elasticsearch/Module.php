<?php
/**
 * @link https://github.com/gromver/yii2-cmf.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-grom/blob/master/LICENSE
 * @package yii2-cmf
 * @version 1.0.0
 */

namespace gromver\platform\frontend\modules\elasticsearch;

use gromver\platform\frontend\interfaces\MenuRouterInterface;
use gromver\platform\frontend\modules\elasticsearch\components\MenuRouterSearch;

/**
 * Class Module
 * @package yii2-cmf
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class Module extends \gromver\platform\common\modules\elasticsearch\Module implements MenuRouterInterface
{
    public $controllerNamespace = 'gromver\platform\frontend\modules\elasticsearch\controllers';

    /**
     * @inheritdoc
     */
    public function getMenuRouter()
    {
        return MenuRouterSearch::className();
    }
}
