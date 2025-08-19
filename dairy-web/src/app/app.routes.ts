import { Routes } from '@angular/router';
import { HomeComponent } from './pages/home/home';
import { DairyComponent } from './pages/dairy/dairy';
import { SugarComponent } from './pages/sugar/sugar';
import { Gold } from './pages/gold/gold';
import { AboutUsComponent } from './pages/about/about';
import { Contact } from './pages/contact/contact';
import { Footer } from './pages/footer/footer';

export const routes: Routes = [
  { path: '', component: HomeComponent },
  { path: 'dairy', component: DairyComponent },
  { path: 'sugar', component: SugarComponent },
  { path: 'gold', component: Gold },
  { path: 'about', component: AboutUsComponent },
  { path: 'contact', component: Contact },
  { path: 'footer', component: Footer },
//   { path: '**', redirectTo: '' }
];