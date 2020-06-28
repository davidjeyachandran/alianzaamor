<?php

/**
 * @file
 * Contains Drupal\aa_whatsapp\Form\SettingsForm.
 */

namespace Drupal\aa_whatsapp\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\NodeInterface;
use Drupal\ssp_hiring_packet\HiringPacketManager;

class WhatsAppSendMessageForm extends FormBase {

  /**
   * Config settings.
   *
   * @var string
   */
  const SETTINGS = 'aa_whatsapp.settings.node';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'aa_whatsapp.settings.node';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $node = \Drupal::routeMatch()->getParameter('node');
    if (!$node instanceof NodeInterface) {
      return [];
    }


    $form['whatsapp_send_message'] = [
      '#type' => 'submit',
      '#value' => t('🤖💬 Send message'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $node = \Drupal::routeMatch()->getParameter('node');
    if (!$node instanceof NodeInterface) {
      return [];
    }

    $participants = $node->get('field_users_to_deliver')->referencedEntities();
    $whatsapp = \Drupal::service('whatsapp.maytapi');
    $intro_message = <<<EOF
Hola @full_name, Tu próxima entrega de víveres es *@title*
Por favor ven con tu Cédula: @id_or_dni Tu número es: @uid
EOF;
    $desc_message = <<<EOF
Te escribimos del Fundación Proyecto Familia en conjunto con Alianza de Amor.
Si puedes, por favor colabora con un Sol. Para confirmar tu asistencia por favor ingresa la pagina con tu Cédula/DNI.
Click en “Mi Entrega” y despues en el butón “Confirmar”. Puedes también confirmar tus datos en tu
Perfil - hay varios inscritos con el distrito “Arequipa".
https://alianzadeamoraqp.org/user Yanahuara, Club Internacional https://goo.gl/maps/XeXzQhVKyjgBhNna6
message
EOF;
    $complete_message = $intro_message . ' ' . $desc_message . ' ';
    /** @var \Drupal\user\UserInterface $participant */
    foreach ($participants as $participant) {
      // dpm($participant->field_celular->value);
      $full_name = $participant->field_first_names->value . ' ' . $participant->field_last_names->value;
      $message_args = t($complete_message, [
        '@full_name' => $full_name,
        '@title' => $node->label(),
        '@id_or_dni' => $participant->label(),
        '@uid' => $participant->id(),
      ]);
      // We render since we want the translation ready since
      // the output is whatsapp and drupal doesn't translate it for us.
      // dpm($message_args->render());
      $message_formatted = $message_args->render();
      // dpm($whatsapp->sendMessage($participant->field_celular->value, $message_formatted));
    }

  }

}
