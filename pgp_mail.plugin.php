<?php
namespace Habari;

class PGP_Mail extends Plugin
{
	private $textdomain = 'pgpmail';

	private function get_gpg()
	{
		putenv("GNUPGHOME=" . Site::get_dir('user') . "/.gnupg");
		return new \gnupg();
	}

	public function filter_mail($mail)
	{
		// $gpg = $this->get_gpg();
		// $gpg->addencryptkey($key['fingerprint']);
		// $mail['message'] = $gpg->encrypt($mail['message']);
		// $mail['headers']['foo'] = 'bar'; // Workaround for PHP bug where there is an unnecessary newline at the end of the headers which can cause an unencrypted newline before the encrypted content
		return $mail;
	}

	public function filter_plugin_config( $actions )
	{
		$actions['addkey'] = _t('Add key', $this->textdomain);
		$actions['listkeys'] = _t('List Keys', $this->textdomain);
		return $actions;
	}

	public function action_plugin_ui_addkey()
	{
		$ui = new FormUI( strtolower( get_class( $this ) ) );
		$ui->append( FormControlTextArea::create('newkey')->label('Paste key to add here:') );
		$ui->newkey->add_validator(array($this, 'save_new_key'));
		$ui->append('submit', 'save', _t('Save'));
		$ui->out();
	}

	public function action_plugin_ui_listkeys()
	{
		$gpg = $this->get_gpg();
		$list = $gpg->keyinfo('');
		print_r($list);
	}

	public function save_new_key($form)
	{
		$gpg = $this->get_gpg();
		$gpg->seterrormode(\gnupg::ERROR_EXCEPTION);
		$info = $gpg->import($form->newkey->value);
		return $info;
	}
}

?>