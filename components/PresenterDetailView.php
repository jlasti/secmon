<?php
namespace app\components;

use app\models\BasePresenter;

class PresenterDetailView extends \yii\widgets\DetailView
{
	protected function normalizeAttributes()
	{
		parent::normalizeAttributes();

		if($this->model instanceof BasePresenter)
		{
			foreach($this->attributes as &$attribute)
			{
				if(isset($attribute['attribute']))
				{
					$attribute['label'] = $this->model->getAttributeLabel($attribute['attribute']);
				}
			}
		}
	}
}