// External Dependencies
import React, { Component } from 'react';
import ItemElement from './components/ItemElement';

// Internal Dependencies
import './style.css';

/**
 * A custom Divi module for displaying questions for the SE journey.
 * This renders the view on the live builder.
 */
class SEQuestionForm extends Component {

  /**
   * The index of the question we are looking at
   *
   * @type {Number}
   */
  currentIndex = 0;

  fadeStateClass = ' visible';

  /**
   * An array of the questions
   *
   * @type {Array}
   */
  items = [];

  /**
   * The slug for this module
   * @type {String}
   */
  static slug = 'copr_se_question_form';

  /**
   * Build the class
   */
  constructor() {
    super();
    this.state = {
      isLast: false,
      item: '',
      number: 0,
    };
  }

  /**
   * Set the current item
   *
   * @param {Boolean} [increment=true] Do you want to increment to the next item?
   * @param {Boolean} [decrement=false] Do you want to decrement to the previous item?
   */
  setItem(increment = true, decrement = false) {
    if (increment) {
      if (this.items.length === (this.currentIndex + 1)) {
        return;
      }
      this.currentIndex += 1;
    } else if (decrement) {
      if (this.currentIndex === 0) {
        return;
      }
      this.currentIndex -= 1;
    }
    const isLast = (this.items.length === (this.currentIndex + 1));
    const item = this.items[this.currentIndex];
    const number = this.currentIndex + 1;
    this.setState({ isLast, item, number });
  }

  /**
   * Get the next item
   *
   * @return {void}
   */
  getNextItem() {
    this.fadeStateClass = ' hidden';
    setTimeout(() => {
      this.fadeStateClass = ' visible';
      this.setItem(true);
    }, 300);
  }

  /**
   * Get the previous item
   *
   * @return {void}
   */
  getPreviousItem() {
    this.fadeStateClass = ' hidden';
    setTimeout(() => {
      this.fadeStateClass = ' visible';
      this.setItem(false, true);
    }, 300);
  }

  /**
   * Render the view to the screen
   *
   * @return {object} A React object
   */
  render() {
    console.log(this.props);
    this.items = this.props.questions.split("\n");
    if (this.state.item === '') {
      this.setItem(false);
    }
    return (
      <div className={`copr-fade-element${this.fadeStateClass}`}>
        <ItemElement isLast={this.state.isLast} item={this.state.item} number={this.state.number} onNextClicked={this.getNextItem.bind(this)} onPreviousClicked={this.getPreviousItem.bind(this)} />
      </div>
    );
  }
}

export default SEQuestionForm;
