<?php
namespace jakharbek\core\behaviors;

/**
 *
 * @author Jakhar <javhar_work@mail.ru>
 *
 */
use noam148\imagemanager\assets\ImageManagerInputAsset;
use Yii;
use yii\behaviors\AttributeBehavior;
use yii\db\ActiveRecord;

/**
 * Class DateTimeBehavior
 * @package jakharbek\core\behaviors
 */
class DateTimeBehavior extends AttributeBehavior
{
    /**
     * @var
     */
    public $attribute;
    /**
     * @var string
     */
    public $format = 'd-m-Y H:i:s';
    /**
     * @var null
     */
    public $disableScenarios = null;
    /**
     * @var null
     */
    public $old_value = null;
    /**
     * @var null
     */
    public $default = null;

    /**
     * @return array
     */
    public function events()
    {
        return [


            ActiveRecord::EVENT_AFTER_FIND => 'visibleDate',
            ActiveRecord::EVENT_BEFORE_VALIDATE  => 'dbDate',
            ActiveRecord::EVENT_AFTER_VALIDATE  => 'after',
            ActiveRecord::EVENT_BEFORE_INSERT  => 'dbDate',
            ActiveRecord::EVENT_BEFORE_UPDATE  => 'dbDate',
        ];
    }

    /**
     * @return void
     */
    public function initial(){
        if(in_array($this->owner->scenario,$this->disableScenarios)){return;}
        if((strlen($this->owner->{$this->attribute}))){ return;}
        if($this->default !== null){
            $this->owner->{$this->attribute} = $this->default;
        }
        if($this->default == 'today'){
            $this->owner->{$this->attribute} = Yii::$app->formatter->asDatetime(time(),$this->format);
        }
    }

    /**
     * @return mixed
     */
    public function visibleDate()
    {
        if(in_array($this->owner->scenario,$this->disableScenarios)){return $this->owner->{$this->attribute};}
        $this->owner->{$this->attribute} = Yii::$app->formatter->asDatetime($this->owner->{$this->attribute},$this->format);
    }

    /**
     * @return mixed|void
     */
    public function dbDate()
    {
            $this->initial();
            if($this->owner->hasErrors()){return;}
            $this->old_value = $this->owner->{$this->attribute};
            if(in_array($this->owner->scenario,$this->disableScenarios)){return $this->owner->{$this->attribute};}
            $this->owner->{$this->attribute} = strtotime($this->owner->{$this->attribute});
    }

    /**
     * @return mixed|void
     */
    public function after(){
        $this->owner->{$this->attribute} = $this->old_value;
    }

    /**
     * @return string 09.10.2017
     */
    public function getDateTitle($date = 'date_publish'){
        $timestamp = strtotime($this->owner->{$date});
        return Yii::$app->formatter->asDatetime($timestamp,"php:d.m.Y");
    }
    /**
     * @return string 09 февраля 19:00
     */
    public function getDateTimeTitle($date = 'date_publish'){
        $timestamp = strtotime($this->owner->{$date});
        return Yii::$app->formatter->asDatetime($timestamp,"php:d F H:m");
    }
    public function getMonthTitle($date = 'date_publish'){
        $timestamp = strtotime($this->owner->{$date});
        return Yii::$app->formatter->asDatetime($timestamp,"php:d F");
    }
    /**
     * @return string //24.03
     */
    public function getDateShortTitle($date = 'date_publish'){
        $timestamp = strtotime($this->owner->{$date});
        return Yii::$app->formatter->asDatetime($timestamp,"php:d.m");
    }
    /**
     * @return string //08:00
     */
    public function getTimeTitle($date = 'date_publish'){
        $timestamp = strtotime($this->owner->{$date});
        return Yii::$app->formatter->asDatetime($timestamp,"php:H:m");
    }
    /**
     * @return string //08:00
     */
    public function getYearTitle($date = 'date_publish'){
        $timestamp = strtotime($this->owner->{$date});
        return Yii::$app->formatter->asDatetime($timestamp,"php:Y");
    }
    /**
     * @return string //08:00
     */
    public function getDayTitle($date = 'date_publish'){
        $timestamp = strtotime($this->owner->{$date});
        return Yii::$app->formatter->asDatetime($timestamp,"php:d");
    }

}