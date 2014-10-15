<?php
/**
 * @link https://github.com/menst/yii2-cms.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/menst/yii2-cms/blob/master/LICENSE
 * @package yii2-cms
 * @version 1.0.0
 */

namespace menst\cms\common\widgets;


use menst\cms\common\models\search\ActiveDocument;
use menst\cms\common\models\search\Search;
use Yii;
use yii\caching\Cache;
use yii\data\ActiveDataProvider;
use yii\di\Instance;

/*
 * $query->query = [
    'filtered' => [
        'filter' => [
            'and' => [
                [
                    'not' => [
                        'and' => [
                            [
                                'exists' => ['field' => 'published']
                            ],
                            [
                                'term' => ['published' => false]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]
];
 */

/**
 * Class SearchResults
 * @package yii2-cms
 * @author Gayazov Roman <m.e.n.s.t@yandex.ru>
 *
 * @property array $filters
 */
class SearchResults extends Widget {
    const CACHE_KEY = 'SearchResults';

    /**
     * @ignore
     */
    public $types;
    public $query;
    /**
     * @var array
     * @ignore
     */
    public $highlight = [
        'fields' => [
            'title' => ["type" => "plain", 'no_match_size' => 150],
            'text' => ["type" => "plain", 'no_match_size' => 150]
        ]
    ];
    /**
     * @type list
     * @items caches
     * @editable
     * @empty no cache
     */
    public $cache;
    public $cacheDuration = 3600;
    /**
     * @type list
     * @items layouts
     * @editable
     */
    public $layout = 'search/results';
    /**
     * @type list
     * @items itemLayouts
     * @editable
     */
    public $itemLayout = '_itemDefault';
    public $pageSize = 10;

    private $_filters;


    public function init()
    {
        parent::init();

        if (!isset($this->types)) {
            $this->types = ActiveDocument::registeredTypes();
        }

        Search::getDb()->open();    //проверяем коннект к elasticSearch
    }

    protected function launch()
    {
        $query = Search::find();
        $query->query = [
            'filtered' => [
                'filter' => [
                    'and' => [
                        'filters' => $this->filters,
                        //'_cache' => true,
                        //'_cache_key' =>
                    ]
                ]
            ],
        ];

        if (!empty($this->query)) {
            $query->query['filtered']['query']['multi_match'] = ['query' => $this->query, 'fields' => ['_all']];
        }

        //чтоб в ActiveQuery задать фильтр по типу надо обязательно задать фильтр по индексу
        //$query->index = 'cms';
        //$query->type = 'page';

        $query->highlight = $this->highlight;

        echo $this->render($this->layout, [
            'dataProvider' => new ActiveDataProvider([
                    'query' => $query,
                    'pagination' => [
                        'pageSize' => $this->pageSize
                    ]
                ]),
            'itemLayout' => $this->itemLayout
        ]);
    }

    protected function collectFilters()
    {
        $filters = [];
        foreach ($this->types as $type) {
            if ($documentClass = ActiveDocument::findDocumentByType($type)) {
                /** @var ActiveDocument $documentClass */
                $conditions = $documentClass::filter();
                foreach ($conditions as $condition) {
                    $filters[json_encode($condition)] = $condition;
                }
            }
        }

        return array_values($filters);
    }

    /**
     * @return array|mixed
     */
    protected function getFilters()
    {
        if (!isset($this->_filters)) {
            if ($this->cache) {
                /** @var Cache $cache */
                $cache = Instance::ensure($this->cache, Cache::className());
                $this->_filters = $cache->get([self::CACHE_KEY, $this->types]);
                if ($this->_filters === false) {
                    //echo 'CACHING SEARCH!';
                    $this->_filters = $this->collectFilters();
                    $cache->set([self::CACHE_KEY, $this->types], $this->_filters, $this->cacheDuration);
                }
            } else {
                $this->_filters = $this->collectFilters();
            }
        }

        return $this->_filters;
    }

    public static function caches()
    {
        return [
            'cache' => 'cache'
        ];
    }

    public static function layouts()
    {
        return [
            'search/results' => Yii::t('menst.cms', 'Default'),
        ];
    }

    public static function itemLayouts()
    {
        return [
            '_itemDefault' => Yii::t('menst.cms', 'Default'),
        ];
    }
}