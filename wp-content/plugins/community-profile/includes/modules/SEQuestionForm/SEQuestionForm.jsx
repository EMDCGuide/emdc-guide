// External Dependencies
import React, { Component } from 'react';

// Internal Dependencies
import './style.css';

/**
 * A custom Divi module for displaying questions for the SE journey.
 * This renders the view on the live builder.
 */
class SEQuestionForm extends Component {

  /**
   * The slug for this module
   * @type {String}
   */
  static slug = 'copr_se_question_form';

  /**
   * Render the view to the screen
   *
   * @return {object} A React object
   */
  render() {
    const Content = this.props.content;

    return (
      <h1>
        <Content/>
      </h1>
    );
  }
}

export default SEQuestionForm;
