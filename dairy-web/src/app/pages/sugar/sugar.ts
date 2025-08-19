import { Component } from '@angular/core';

@Component({
  selector: 'app-sugar',
  imports: [],
  templateUrl: './sugar.html',
  styleUrl: './sugar.scss'
})
export class SugarComponent {
  // Add any properties or methods needed for the Sugar component
  sugarData = { years: '10+', clients: '20+ sugar mills', retention: '95%' };
  constructor() {
    // Initialize any data or services needed for this component
  }
}
