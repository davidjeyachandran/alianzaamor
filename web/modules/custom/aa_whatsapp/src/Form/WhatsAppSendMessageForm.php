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
use Drupal\user\Entity\User;

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
    $user = User::load(\Drupal::currentUser()->id());
    if (!$user->hasRole('equipo') &&
      !$user->hasRole('misionero') &&
      !$user->hasRole('administrator') &&
      !$user->id() !== 1) {
      return [];
    }

    $node = \Drupal::routeMatch()->getParameter('node');
    if (!$node instanceof NodeInterface) {
      return [];
    }
    $was_notified = \Drupal::config('aa_whatsapp.settings')->get('was_notified__node_' . $node->id());
    $form['whatsapp_send_message_info'] = [
      '#type' => 'item',
      '#plain_text' => $was_notified ? t('A bunch of people have been notified via whatsapp.
      If you forgot to notify somebody then please do it individually.')
        : 'By clicking on Send message you will notify a bunch of people via whatsapp. Do it when you are ready.',
    ];
    $form['whatsapp_send_message'] = [
      '#type' => 'submit',
      '#title' => 'hey',
      '#value' => t('🤖💬 Send message'),
      '#disabled' => $was_notified,
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
    $location = $node->field_location->entity;
    $location__map_uri = $location->field_map_url->uri;
    $location__name = $location->getName();
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
https://alianzadeamoraqp.org/user @location__name @location__map_uri
EOF;
    $complete_message = $intro_message . ' ' . $desc_message . ' ';
    $number_of_people = 0;
    /** @var \Drupal\user\UserInterface $participant */
    foreach ($participants as $participant) {
      $full_name = $participant->field_first_names->value . ' ' . $participant->field_last_names->value;
      $message_args = t($complete_message, [
        '@full_name' => $full_name,
        '@title' => $node->label(),
        '@id_or_dni' => $participant->label(),
        '@uid' => $participant->id(),
        '@location__name' => $location__name,
        '@location__map_uri' => $location__map_uri,
      ]);
      // We render since we want the translation ready since
      // the output is whatsapp and drupal doesn't translate it for us.
      $message_formatted = $message_args->render();

      if ($whatsapp->sendMessage($participant->field_celular->value, $message_formatted)) {
        \Drupal::messenger()->addMessage(t('I have sent message to participant @id_or_dni with phone number: @phone_number.', [
          '@id_or_dni' => $participant->label(),
          '@phone_number' => $participant->field_celular->value,
        ]));
        \Drupal::messenger()->addMessage($message_formatted);
        $number_of_people++;
      }
    }
    if ($number_of_people) {
      \Drupal::messenger()->addMessage(t('I have notified @number_of_people people via whatsapp.', [
        '@number_of_people' => $number_of_people,
      ]));
      \Drupal::configFactory()->getEditable('aa_whatsapp.settings')
        ->set('was_notified__node_' . $node->id(), TRUE)
        ->save();
    }
  }

}
