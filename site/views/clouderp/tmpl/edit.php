<?php
/**
 * @package    Joomlaphoto
 * @author     Claude Lautenschlager
 * @copyright  Copyright (C) 2017 - 2019 All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 * Aufruf: http://localhost/wild/administrator/index.php?option=com_joomlaphoto&view=joomlaphoto&layout=edit&folder=sliders/primaer&file=50881488_285889272100499_1391177853210460160_o.jpg
 */
defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.tooltip');

JFactory::getDocument()->addScriptDeclaration("
	Joomla.submitbutton = function(task)
	{
		
//Optionen: siehe http://nhn.github.io/tui.image-editor/latest/ImageEditor#toDataURL			
		options=$this->saveOptions;
		data={path:paramFolder, filename:paramFileName, data:imageEditor.toDataURL(options)};
		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				result=JSON.parse(this.response);
				alert(result.text);	
			}
			
		};
		xhttp.open('POST', 'index.php?option=com_joomlaphoto&task=photo.upload', true);
		xhttp.setRequestHeader('Content-type', 'application/json');
		xhttp.send(JSON.stringify(data));
		return false;
	};
");

?>
<div id="tui-image-editor-container"></div>
<script>

	var locale_de_CH = { // override default English locale to your custom
		'Crop': 'Zuschneiden',
		'Delete-all': 'Alle Löschen',
		'Grayscale' : 'Grau',
		'Blur':'Verwischen',
		'Invert':'Invertieren',
		'Apply':'Anwenden',
		'Cancel': 'Abbrechen',
		'Brightness':'Leuchtkraft',
		'Sharpen':'Schärfen',
		'Remove White':'Weiss entfernen',
		'Blend':'Mischen',
		'Tint':'Farbton',
		'Rotate':'Drehen',
		'Flip':'Spiegeln',
		'Draw':'Zeichnen',
		'Shape':'Figuren',
		'Mask':'Maskieren (überladen)',
		'Filter':'Werkzeuge ein/ausblenden',
		'Load':'Bild ersetzen'
		// etc...
	};
	

	var paramFileName= '<?php echo $this->paramFileName; ?>';
	var paramFolder= '<?php echo $this->paramFolder; ?>';
	var imageEditor = new tui.ImageEditor('#tui-image-editor-container', {
             includeUI: {
                 loadImage: {
                     path: '<?php echo $this->loadFileName; ?>',
                     name: 'Curling_Events'
                 },
                 theme: <?php echo $this->theme; ?>,
                 initMenu: 'filter',
                 menuBarPosition: '<?php echo $this->buttonlist; ?>',
				 locale:  locale_de_CH
             },
             cssMaxWidth: 700,
             cssMaxHeight: <?php echo $this->maxcssheight; ?>,
             usageStatistics: false
         });

         window.onresize = function() {
             imageEditor.ui.resizeEditor();
         }

</script>

