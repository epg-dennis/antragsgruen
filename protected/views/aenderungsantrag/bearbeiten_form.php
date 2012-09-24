<?php

/**
 * @var AenderungsantragController $this
 * @var string $mode
 * @var Antrag $antrag
 * @var Aenderungsantrag $aenderungsantrag
 * @var array $hiddens
 * @var bool $js_protection
 */

Yii::app()->getClientScript()->registerScriptFile(Yii::app()->request->baseUrl . '/js/ckeditor/ckeditor.js');
Yii::app()->getClientScript()->registerScriptFile(Yii::app()->request->baseUrl . '/js/ckeditor.bbcode.js');

$this->breadcrumbs = array(
	CHtml::encode($antrag->veranstaltung0->name_kurz) => "/",
	"Antrag" => "/antrag/anzeige/?id=" . $antrag->id,
	'Neuer Änderungsantrag'
);?>


<h1 class="well">Änderungsantrag stellen: <?php echo CHtml::encode($antrag->name)?></h1>

<?php
/** @var TbActiveForm $form */
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'     => 'horizontalForm',
	'type'   => 'horizontal',
	"action" => "/aenderungsantrag/neu/",
));

foreach ($hiddens as $name=>$value) {
	echo '<input type="hidden" name="' . CHtml::encode($name) . '" value="' . CHtml::encode($value) . '">';
}

?>
<div class="antrags_text_holder ae_absatzwahl_modus well well_first" style="overflow: auto;">
	<?php
	Yii::app()->user->setFlash("info", "Bitte wähle nun die Absätze aus, die geändert werden sollen. Du kannst dann die beantragte neue Fassung sowie die Begründung für den Änderungsantrag angeben.");
	$this->widget('bootstrap.widgets.TbAlert');

	if ($js_protection) { ?>
	<div class="js_protection_hint">ACHTUNG: Um diese Funktion zu nutzen, muss entweder JavaScript aktiviert sein, oder du musst eingeloggt sein.</div>
	<?php } ?>

	<h3><label for="Aenderungsantrag_name_neu">Neuer Titel</label></h3>
	<br>
	<input id="Aenderungsantrag_name_neu" type="text" value="<?php echo CHtml::encode($aenderungsantrag->name_neu); ?>" name="Aenderungsantrag[name_neu]" style="width: 550px; margin-left: 52px;">
	<br>
	<br>
	<h3>Neuer Antragstext</h3>
	<br>

	<div class="textholder consolidated">
		<?php

		$absae = $antrag->getParagraphs();
		$text_pre = ($mode == "bearbeiten" ? $aenderungsantrag->getDiffParagraphs() : null);
		foreach ($absae as $i=> $abs) {
			/** @var AntragAbsatz $abs */
			echo "<div class='row-fluid row-absatz' id='absatz_" . $i . "'>";

			echo "<div class='absatz_text orig antragabsatz_holder antrags_text_holder_nummern' ";
			if ($text_pre && $text_pre[$i] != "") echo " style='display: none;'";
			echo ">" . $abs->str_html . "</div>";
			echo "<div class='antragstext_diff' ";
			if (!$text_pre || $text_pre[$i] == "") echo "style='display: none;'";
			echo ">";
			if ($text_pre && $text_pre[$i] != "") echo DiffUtils::renderBBCodeDiff2HTML($abs->str_bbcode, $text_pre[$i]);
			else echo $abs->str_html_plain;
			echo "</div>";

			echo "<div class='ae_text_holder'>
			<label><input type='checkbox' name='change_text[$i]' data-absatz='$i' class='change_checkbox' ";
			if ($text_pre && $text_pre[$i] != "") echo "checked";
			echo "> Ändern<br></label>
			<h4>Neue Fassung</h4>
			<textarea id='neu_text_$i' name='neu_text[$i]' style='width: 550px; height: 200px;'>";
			$str_neu = ($text_pre && $text_pre[$i] != "" ? $text_pre[$i] : $abs->str_bbcode);
			echo CHtml::encode($str_neu) . "</textarea></div>";

			echo "</div>";
		}
		?>
	</div>
</div>

<div class="well">
	<h3><label for="ae_begruendung">Begründung für den Änderungsantrag</label></h3>
	<br>
	<textarea name='ae_begruendung' id="ae_begruendung" style='width: 550px; height: 200px;'><?php
		echo CHtml::encode($aenderungsantrag->aenderung_begruendung);
		?></textarea>

	<?php if ($mode == "bearbeiten") { ?>
	<div class="ae_select_confirm" style="margin-top: 20px;">
		<?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=> 'submit', 'type'=> 'primary', 'icon'=> 'ok white', 'label'=> 'Speichern')); ?>
	</div>
<?php } ?>
    <br><br>


</div>

<?php if ($mode == "neu") {
	/** @var Person $antragstellerin */
	?>
<div class="well">
	<h3>AntragstellerIn</h3>
	<br>

	<?php echo $form->textFieldRow($antragstellerin, 'name'); ?>

	<?php echo $form->textFieldRow($antragstellerin, 'email'); ?>

	<?php echo $form->textFieldRow($antragstellerin, 'telefon'); ?>


	<div class="ae_select_confirm">
		<?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=> 'submit', 'type'=> 'primary', 'icon'=> 'ok white', 'label'=> 'Änderungsantrag stellen')); ?>
	</div>

	<br><br>

</div>
<?php } ?>
<script>
	var antrag_id = <?=$antrag["id"]?>;

	function antragstext_init_aes() {
		"use strict";
		$(".ae_text_holder input.change_checkbox").not(':checked').parents(".ae_text_holder").hide();
		$(".change_checkbox").parents("label").hide();
		$(".ae_text_holder textarea").each(function () {
			CKEDITOR.replace($(this).attr("id"), {'toolbar':'Animexx', 'customConfig':"/js/ckconfig.js", width:550 });
		});
		CKEDITOR.replace("ae_begruendung", {'toolbar':'Animexx', 'customConfig':"/js/ckconfig.js", width:680 });

		$(".ae_absatzwahl_modus .antragabsatz_holder .text").click(function (ev) {
			var $abs = $(this).parents(".row-absatz");
			$abs.find(".change_checkbox").prop("checked", true);
			$abs.find(".ae_text_holder").show().css("display", "block");
			$abs.find(".orig").hide();
			$abs.find(".antragstext_diff").show();
		});
		$(".ae_absatzwahl_modus .antragstext_diff").click(function(ev) {
			var $abs = $(this).parents(".row-absatz");
			$abs.find(".change_checkbox").prop("checked", false);
			$abs.find(".ae_text_holder").hide();
			$abs.find(".orig").show();
			$abs.find(".antragstext_diff").hide();
		});

		window.setTimeout(antragstext_show_diff, 3000);
	}

	function antragstext_show_diff() {
		var abss = {},
			str;
		$(".change_checkbox:checked").each(function () {
			var absatznr = $(this).data("absatz");
			str = CKEDITOR.instances["neu_text_" + absatznr].getData();
			abss[absatznr] = str;
		});
		$.ajax({ "url":"/aenderungsantrag/ajaxCalcDiff/", "dataType":"json", "type":"POST", "data":{ "antrag_id":antrag_id, "absaetze":abss }, "error":function (xht, status) {
			window.setTimeout(antragstext_show_diff, 10000);
		}, "success":function (dat) {
			for (var i in dat) if (dat.hasOwnProperty(i)) {
				$("#absatz_" + i + " .antragstext_diff").html(dat[i]);
			}
		}});
		window.setTimeout(antragstext_show_diff, 3000);
	}

	$(antragstext_init_aes);
</script>

<?php $this->endWidget(); ?>