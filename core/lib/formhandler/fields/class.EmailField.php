<?php
include_once ('class.TextField.php');
/**
 * class EmailField
 *
 * Create an emailfield
 *
 * @author MarkoB
 * @package FormHandler
 * @subpackage Fields
 */
class EmailField extends TextField
{
	/**
     * TextField::getField()
     *
     * Return the HTML of the field
     *
     * @return string: the html
     * @access public
     * @author Teye Heimans
     */
	function getField()
	{
		// view mode enabled ?
		if( $this -> getViewMode() )
		{
			// get the view value..
			return $this -> _getViewValue();
		}

		return sprintf(
		'<input type="email" name="%s" id="%1$s" value="%s" size="%d" %s'. FH_XHTML_CLOSE .'>%s',
		$this->_sName,
		(isset($this->_mValue) ? htmlspecialchars($this->_mValue):''),
		$this->_iSize,
		(!empty($this->_iMaxlength) ? 'maxlength="'.$this->_iMaxlength.'" ':'').
		(isset($this->_iTabIndex) ? 'tabindex="'.$this->_iTabIndex.'" ' : '').
		(isset($this->_sExtra) ? ' '.$this->_sExtra.' ' :''),
		(isset($this->_sExtraAfter) ? $this->_sExtraAfter :'')
		);
	}
}

?>
