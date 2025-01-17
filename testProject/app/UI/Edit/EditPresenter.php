<?php
namespace App\UI\Edit;

use Nette;
use Nette\Application\UI\Form;

final class EditPresenter extends Nette\Application\UI\Presenter
{
	public function __construct(
		private Nette\Database\Explorer $database,
	) {
	}

// komponenta post formuláře
  protected function createComponentPostForm(): Form
  {
    $form = new Form;
    $form->addText('title', 'Titulek:')
      ->setRequired();
    $form->addTextArea('content', 'Obsah:')
      ->setRequired();
  
    $form->addSubmit('send', 'Uložit a publikovat');
    $form->onSuccess[] = $this->postFormSucceeded(...);
  
    return $form;
  }


  // ukládání nového příspěvku

  private function postFormSucceeded(array $data): void
  {
    $postId = $this->getParameter('postId');
  
    if ($postId) {
      $post = $this->database
        ->table('posts')
        ->get($postId);
      $post->update($data);
  
    } else {
      $post = $this->database
        ->table('posts')
        ->insert($data);
    }
  
    $this->flashMessage('Příspěvek byl úspěšně publikován.', 'success');
    $this->redirect('Post:show', $post->id);
  }
  

public function renderEdit(int $postId): void
{
	$post = $this->database
		->table('posts')
		->get($postId);

	if (!$post) {
		$this->error('Post not found');
	}

	$this->getComponent('postForm')
		->setDefaults($post->toArray());
}


public function startup(): void
{
	parent::startup();

	if (!$this->getUser()->isLoggedIn()) {
		$this->redirect('Sign:in');
	}
}



}
