<?php
namespace app\components\filter;

use app\components\filter\BaseFilterRule;
use Symfony\Component\Validator\Constraints\Date;
use yii\base\InvalidParamException;
use yii\base\Application;


class DateFilterRule extends BaseFilterRule
{
	/**
	 * @inheritdoc
	 */
	public function isStatic()
	{
		return false;
	}

    /**
     * @inheritdoc
     */
    public static function type()
    {
        return FilterTypeEnum::DATE;
    }

    /**
     * @inheritdoc
     */
    public static function title()
    {
        return 'Date';
    }

	/**
	 * @inheritdoc
	 */
	public static function operators()
	{
		return [
			'>',
			'<',
			'>=',
			'<=',
			'!=',
			'=',
            'Last',
		];
	}

    /**
	 * @inheritdoc
	 */
	public static function rules()
    {
		return array_merge(parent::rules(), [
			['value', 'date', 'format' => 'yyyy-MM-dd HH:mm', 'when' => function ($model) {
		        return $model->operator != 'Last';
            }],
            ['value', 'match', 'pattern' => '/^\d{1,5}[YMWDHmS]{1}$/', 'when' => function ($model) {
		        return $model->operator == 'Last';
            }, 'message' => 'Enter valid format(nY/nM/nW/nD/nH/nm/nS)!'],
		]);
	}

    /**
     * @inheritdoc
     */
    protected function _applyInternal(&$collection)
    {
        $dateLast = null;
        if($this->operator == 'Last') {
            $timeUnit = substr($this->value, -1);
            if($timeUnit == 'Y' || $timeUnit == 'M' || $timeUnit == 'W' || $timeUnit == 'D') {
                $range = 'P' . $this->value;
            } else if ($timeUnit == 'm') {
                $range = 'PT' . substr($this->value, 0, -1) . 'M';
            } else {
                $range = 'PT' . $this->value;
            }
            $dateLast = new \DateTime('now');
            $dateLast->sub(new \DateInterval($range));
        }
        switch($this->logic_operator)
        {
            case 'OR':
                $this->operator == 'Last' ? $collection->orWhere(['>=', $this->column, $dateLast->format('Y-m-d H:i:s')]) : $collection->orWhere([$this->operator, $this->column, $this->value.':00']);
                break;
            case 'AND':
            default:
                $this->operator == 'Last' ? $collection->andWhere(['>=', $this->column, $dateLast->format('Y-m-d H:i:s')]) : $collection->andWhere([$this->operator, $this->column, $this->value.':00']);
                break;
        }
    }
}