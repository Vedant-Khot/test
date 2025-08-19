import { Component, signal } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { HttpClientModule } from '@angular/common/http';
import { CommonModule } from '@angular/common';
import { Footer } from "./pages/footer/footer";

@Component({
  selector: 'app-root',
  standalone: true, // ✅ required for standalone components
  imports: [RouterOutlet, HttpClientModule, CommonModule, Footer],
  templateUrl: './app.html',
  styleUrls: ['./app.scss']
})
export class App {
  protected readonly title = signal('dairy-web');
}
