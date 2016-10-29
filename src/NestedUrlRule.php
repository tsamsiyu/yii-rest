<?php namespace tsamsiyu\yii\rest;

use Yii;
use yii\base\Object;
use yii\helpers\Inflector;
use yii\web\UrlRule;
use yii\web\UrlRuleInterface;

/**
 * @property \yii\web\UrlRUle[] $coreRules
 *
 * Class ResourceUrlRule
 * @package tsamsiyu\yii\rest
 */
class NestedUrlRule extends Object implements UrlRuleInterface
{
    const INDEX_ACTION = 'index';
    const VIEW_ACTION = 'view';
    const CREATE_ACTION = 'create';
    const UPDATE_ACTION = 'update';
    const DELETE_ACTION = 'delete';
    const OPTIONS_ACTION = 'options';

    /**
     * @var string
     */
    public $prefix = '';

    /**
     * @var array
     */
    public $resources = [];

    /**
     * This parameter is the reflection of expected resource id query param. It presents the name of query param
     * that will be put to $_GET array as process resource id
     *
     * If you set this property then it will be exactly used to populate global $_GET array, otherwise
     * the next formula will be used: `{resourceName}Id`
     *
     * @var string
     */
    public $entryResourceId = 'id';

    /**
     * @var array
     */
    public $only = [];

    /**
     * @var array
     */
    public $except = [];

    /**
     * @var
     */
    public $controller;

    private $defaultRoutesMap = [
        self::CREATE_ACTION => '{controller}/create',
        self::UPDATE_ACTION => '{controller}/update',
        self::INDEX_ACTION => '{controller}/index',
        self::VIEW_ACTION => '{controller}/view',
        self::DELETE_ACTION => '{controller}/delete',
        self::OPTIONS_ACTION => '{controller}/options'
    ];

    /**
     * @var array
     */
    public $routesMap = [];

    /**
     * @var array
     */
    public $coreRuleConfig = [
        'class' => UrlRule::class
    ];

    private $defaultRoutesDescription = [
        ['GET', '{permanentUrl}', self::INDEX_ACTION],
        ['GET', '{permanentUrl}/{resourceId}', self::VIEW_ACTION],
        ['POST', '{permanentUrl}', self::CREATE_ACTION],
        [['PATCH', 'PUT'], '{permanentUrl}/{resourceId}', self::UPDATE_ACTION],
        ['DELETE', '{permanentUrl}/{resourceId}', self::DELETE_ACTION],
        ['OPTIONS', '{permanentUrl}/{resourceId}', self::OPTIONS_ACTION],
        ['OPTIONS', '{permanentUrl}', self::OPTIONS_ACTION],
    ];

    /**
     * @var array
     */
    public $routesDescription = [];

    /**
     * @var array
     */
    private $_coreRules;

    /**
     * @param \yii\web\UrlManager $manager
     * @param \yii\web\Request $request
     * @return array|bool
     */
    public function parseRequest($manager, $request)
    {
        foreach ($this->coreRules as $coreRule) {
            /* @var $coreRule \yii\web\UrlRule */
            if (($result = $coreRule->parseRequest($manager, $request)) !== false) {
                Yii::trace("Request parsed with URL rule: {$coreRule->name}", __METHOD__);
                return $result;
            }
        }

        return false;
    }

    /**
     * @param \yii\web\UrlManager $manager
     * @param string $route
     * @param array $params
     * @return bool|string
     */
    public function createUrl($manager, $route, $params)
    {
        foreach ($this->coreRules as $coreRule) {
            /* @var $coreRule \yii\web\UrlRule */
            if (($url = $coreRule->createUrl($manager, $route, $params)) !== false) {
                return $url;
            }
        }

        return false;
    }

    /**
     * @return array
     */
    public function getCoreRules()
    {
        if (!isset($this->_coreRules)) {
            $this->_coreRules = [];
            list($preceding, $resource) = $this->getPermanentUrlSegments();
            $controllerName = $this->getControllerName();
            $isOnlySpecified = is_array($this->only) && count($this->only);
            $except = $this->except;
            $only = $this->only;
            $routesMap = $this->getRoutesMap();
            foreach ($this->getRoutesDescription() as $routeDescription) {
                list($verb, $pattern, $route) = $routeDescription;
                if (!isset($except[$route]) && !($isOnlySpecified && !isset($only[$route]))) {
                    $entryResourceId = $this->entryResourceId ?: $resource . 'Id';
                    $pattern = strtr($pattern, [
                        '{permanentUrl}' => $preceding,
                        '{resourceId}' => "<{$entryResourceId}:\d+>"
                    ]);
                    if (isset($routesMap[$route])) {
                        $action = strtr($routesMap[$route], [
                            '{controller}' => $controllerName
                        ]);
                    } else {
                        $action = strtr($route, [
                            '{controller}' => $controllerName
                        ]);
                    }
                    $rule = $this->coreRuleConfig;
                    $rule['verb'] = $verb;
                    $rule['pattern'] = $pattern;
                    $rule['route'] = $action;
                    $this->_coreRules[] = Yii::createObject($rule);
                }
            }
        }
        return $this->_coreRules;
    }

    public function getControllerName()
    {
        $controller = implode('/', $this->resources);
        if ($this->controller) {
            return strtr($this->controller, ['{controller}' => $controller]);
        } else {
            return $controller;
        }
    }

    /**
     * @return array
     */
    public function getPermanentUrlSegments()
    {
        $permanent = $this->prefix;
        $resources = $this->resources;
        $mainResource = array_pop($resources);
        foreach ($resources as $resource) {
            $permanent .= '/' . Inflector::pluralize($resource) . "/<{$resource}Id:[\d]+>";
        }
        $permanent .= '/' . Inflector::pluralize($mainResource);
        return [$permanent, $mainResource];
    }

    public function getRoutesMap($id = null)
    {
        $map = array_merge($this->defaultRoutesMap, $this->routesMap);
        if ($id) {
            return $map[$id];
        }
        return $map;
    }

    public function getRoutesDescription()
    {
        $description = $this->defaultRoutesDescription;
        $customDescriptions = $this->routesDescription;
        $verbs = 'GET|HEAD|POST|PUT|PATCH|DELETE|OPTIONS';
        foreach ($customDescriptions as $pattern => $action) {
            if (preg_match("/^((?:($verbs),)*($verbs))(?:\\s+(.*))?$/", $pattern, $matches)) {
                $verbs = explode(',', $matches[1]);
                $pattern = isset($matches[4]) ? $matches[4] : '';
            } else {
                $verbs = [];
            }
            $description[] = [$verbs, $pattern, $action];
        }
        return $description;
    }
}