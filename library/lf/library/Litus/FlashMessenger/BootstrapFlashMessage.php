<?php

namespace Litus\FlashMessenger;

/**
 * @author Alan alan.szepieniec@litus.cc
 * 
 * BootstrapFlashMessage -- a flash message that is friendly to
 * the twitter-bootstrap stylesheet
 *
 * Ideally, FlashMessage is be layout-ignorant. Use FlashMessage
 * to create flash messages and use layout-aware classes like
 * this one in the view that displays the flash message
 *
 * Preferred use:
 * (in controller:)
 * $this->view->flashMsg = new FlashMessage( bla bla bla );
 *
 * (in view)
 * echo BootstrapFlashMessage::layout( $flashMsg );
 */
class BootstrapFlashMessage extends FlashMessage
{

    private static $_typeclass = array( FlashMessage::ERROR => "alert-message error",
					FlashMessage::WARNING => "alert-message warning",
					FlashMessage::SUCCESS => "alert-message success",
					FlashMessage::NOTICE => "alert-message notice" );

    /**
     * @var boolean to determine whether we are a regular flash message (false)
     * or a block flash message (true)
     */
    private $_blockMessage = false;

        public function isBlockMessage() {
            return $this->_blockMessage;
        }

        public function setBlockMessage( $blockMessage ) {
            $this->_blockMessage = $blockMessage;
        }

	/**
	 *
	 */
	public static function layout( $flashMessage ) {
	    return new BootstrapFlashMessage( $flashMessage );
	}

    /**
     * Create new BootstrapFlashMessage.
     *
     * @param string $type The FlashMessage's type
     * @param string $title The FlashMessage's title
     * @param string $message The FlashMessage's message
     * @param bool $fullWidth Whether or not the message should be displayed full width
     */
	public function __construct($flashMessage)
	{
		$this->setType( $flashMessage->getType() );
		$this->setTitle ($flashMessage->getTitle() );
		$this->setMessage( $flashMessage->getMessage() );
                $this->setFullWidth( $flashMessage->isFullWidth() );
	}

    /**
     * Return a string version of this message that will be used following an echo call in
     * a view file.
     *
     * @return string
     */
	public function __toString()
	{
		$id = "flashmsg" . rand() . rand();
		return '<div class="content" id="'.$id.'"><div class="' . BootstrapFlashMessage::$_typeclass[$this->getType()] . ( $this->isBlockMessage() ? ' block-message' : '') . '" id='.$id.'>'
			.'<a class="close" href="#" onclick="document.getElementById(\''.$id.'\').innerHTML = \'\';">&times;</a>'
			.'<p><strong>' . $this->getTitle() . '</strong></p>'
			.'<p>' . $this->getMessage() . '</p>'
			.'</div></div>';
	}
}
