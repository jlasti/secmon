<?php
namespace app\models;

class BasePresenter extends \frostealth\presenter\Presenter
{
	/**
	 * Attribute labels like in active record
	 *
	 * @return array Attribute labels
	 */
	public function attributeLabels()
	{
		return [];
	}

	/**
	 * Find attribute label. If it is defined in presenter class, return it, else return label from entity model.
	 *
	 * @param string $attribute Attribute to find label for
	 *
	 * @return string Label
	 */
	public function getAttributeLabel($attribute)
	{
		$labels = $this->attributeLabels();

		return $labels[$attribute] ?? $this->entity->getAttributeLabel($attribute);
	}
}