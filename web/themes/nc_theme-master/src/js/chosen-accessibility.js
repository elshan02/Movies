/**
 * @file
 * A JavaScript file for the theme.
 * Chosen doesn't add labels to its search inputs, so this script adds them.
 * Uses a setTimeout to wait for the Chosen script to load.
 */

(function (Drupal) {
  'use strict';

  // To understand behaviors, see https://www.drupal.org/node/2269515
  Drupal.behaviors.chosen_accessibility = {
    attach: function (context) {

      // prevents scripts from firing in other contexts such as AJAX requests
      if (context !== document) {
        return;
      }

      setTimeout(() => {
        addLabelsToChosen();
      }, 1000);

      /**
       * Add labels to Chosen search inputs by adding a label, creating an id for the input, and associating the label with the input.
       */
      const addLabelsToChosen = () => {
        let labelInnerText;
        let chosenInputs = once('chosen-accessibility', document.querySelectorAll('.chosen-search input'), context);
        let bodyClasses = document.querySelector('body').classList;

        if (bodyClasses.contains('lang-fr')) {
          labelInnerText = 'Mots-clés'; // French
        } else {
          labelInnerText = 'Keywords';
        }

        chosenInputs.forEach((input, i) => {
          const inputID = `chosen-input-${i}`;
          input.setAttribute('id', inputID);
          input.setAttribute('name', inputID);
          const label = document.createElement('label');
          label.innerText = labelInnerText;
          label.setAttribute('for', inputID);
          label.classList.add('visually-hidden');
          input.parentElement.prepend(label);
        });
      }

    },
  };
})(Drupal);
