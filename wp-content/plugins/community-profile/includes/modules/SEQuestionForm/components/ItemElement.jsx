// External Dependencies
import React, { Component } from 'react';

/**
 * The layout for a single question
 */
class ItemElement extends Component {
  /**
   * Render the component.
   *
   * @return {object} The component
   */
  render() {
    const parts = this.props.item.split('|');
    const question = parts[0];

    if (this.props.item === '') {
      return (
        <p><em>Loading...</em></p>
      );
    }

    let formElement = null;
    if (parts[1] === 'text') {
      formElement = this.getTextarea();
    } else if (parts[1] === 'choice') {
      const choices = parts[2].split(',');
      formElement = this.getRadioButtons(choices);
    }
    if (!formElement) {
      return (
        <div class="copr-error-box"><p className="copr-error-message">You defined an incorrect question type or it is malformed.</p></div>
      )
    }
    const nextDisabledClass = (this.props.isLast) ? ' copr-disabled' : '';
    const prevDisabledClass = (this.props.number === 1) ? ' copr-disabled' : '';
    return (
      <div class="copr-question-field-wrapper">
        <form action="#">
          <div class="form-element-wrapper">
            <label>{this.props.number}) {question}</label>
            {formElement}
          </div>
          <div class="copr-form-buttons">
            <div class="copr-width-50">
              <a href="/" class={`copr-previous${prevDisabledClass}`} onClick={this.getPrevious.bind(this)}><span class="dashicons dashicons-arrow-left-alt2"></span> Previous</a> |
              <a href="/" class={`copr-next${nextDisabledClass}`} onClick={this.getNext.bind(this)}>Next <span class="dashicons dashicons-arrow-right-alt2"></span></a>
            </div>
            <div class="copr-width-50 copr-align-right submit">
                <input type="submit" name="submit" value="Save" disabled="disabled" />
            </div>
            <div class="copr-fixed"></div>
          </div>
        </form>
      </div>
    )
  }

  /**
   * Get the next item
   *
   * @param event The event
   * @return {void}
   */
  getNext(event) {
    event.preventDefault();
    this.props.onNextClicked();
  }

  /**
   * Get the previous item
   *
   * @param event The event
   * @return {void}
   */
  getPrevious(event) {
    event.preventDefault();
    this.props.onPreviousClicked();
  }

  /**
   * Get the choices radio buttons
   * @param  {array} choices  The possible choices
   * @return {object}         The HTML object
   */
  getRadioButtons(choices) {
    if (choices.length === 0) {
      return (
        <div class="copr-error-box"><p className="copr-error-message">Missing choices on this question.</p></div>
      )
    }
    const inputs = choices.map((choice, index) =>  this.getRadioButton(choice, index));
    return (
      <div>{inputs}</div>
    )
  }

  /**
   * Get a single radio button
   *
   * @param  {string} choice The value of the choice
   * @param  {Number} index  The index of the choice
   * @return {object}        The HTML object
   */
  getRadioButton(choice, index) {
    const checked = (index === 0) ? 'checked' : '';
    return (
      <div>
        <input type="radio" name="answer" value={choice} checked={checked} />
        <label>{choice}</label>
      </div>
    );
  }

  /**
   * Get the textarea for text based forms.
   *
   * @return {object} The HTML object
   */
  getTextarea() {
    return (
      <textarea name="answer" class="copr-answer-textarea" rows="10"></textarea>
    );
  }
}


export default ItemElement;
