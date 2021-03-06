<?php
/* @var $this TexteController */
/* @var $model Texte */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs = array(
	Yii::t('app', 'Administration') => '/admin',
	Texte::label(2),
);

$this->menu = array(
	array('label'=> Texte::label() . ' ' . Yii::t('app', 'Create'), 'url' => array('create'), "icon" => "plus-sign"),
	array('label'=> Texte::label(2) . ' ' . Yii::t('app', 'Manage'), 'url' => array('admin'), "icon" => "th-list"),
);
?>

<h1 class="well"><?php echo GxHtml::encode(Texte::label(2)); ?></h1>

<div class="well well_first">
	<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=> $dataProvider,
	'itemView'    => '_view',
));
	?>
</div>