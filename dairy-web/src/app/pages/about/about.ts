// about-us.component.ts
import { Component, OnInit, OnDestroy, ElementRef, ViewChild, AfterViewInit } from '@angular/core';
import { trigger, state, style, transition, animate, query, stagger } from '@angular/animations';
import { CommonModule } from '@angular/common';

@Component({
  standalone: true,
  selector: 'app-about-us',
  templateUrl: './about.html',
  styleUrls: ['./about.scss'],
  imports: [CommonModule],
})

export class AboutUsComponent implements OnInit, AfterViewInit, OnDestroy {

   
  @ViewChild('timelineContainer', { static: false }) timelineContainer!: ElementRef;
  
  activeTab: string = 'team';
  
  // Tab configuration
  tabs = [
    { id: 'story', label: 'Our Story', icon: '📖' },
    { id: 'team', label: 'Our Team', icon: '👥' },
    { id: 'careers', label: 'Careers', icon: '🚀' }
  ];

  // Timeline data
  timelineData = [
    {
      year: '1998: The Beginning',
      description: 'Founded by [Founder\'s Name], [Your Company Name] was born from a direct request from a local dairy owner struggling with manual bookkeeping. Our first software was built on-site, solving a real problem.'
    },
    {
      year: 'Early 2000s: Entering the Sugar Sector',
      description: 'Recognizing similar challenges in another core industry, we adapted our expertise to develop our first ERP solution for a sugar factory, focusing on the complexities of cane management.'
    },
    {
      year: 'Mid 2000s: Building the Team',
      description: 'Our team grew, bringing in new developers and support staff who shared our passion for client-focused problem-solving.'
    },
    {
      year: '2010s: Embracing New Technology',
      description: 'We migrated our platforms to more modern, robust technologies (like .NET and SQL), ensuring our clients\' systems were secure, scalable, and future-proof.'
    },
    {
      year: 'Late 2010s: The Gold Standard',
      description: 'We launched our specialized solution for the gold and jewelry industry, addressing the unique needs for high-security inventory and artisan management.'
    },
    {
      year: 'Today: A Trusted Partner',
      description: 'With a portfolio of over [Number] clients across three key industries, we continue to innovate, driven by the same principle we started with in 1998: your success is our success.'
    }
  ];

  // Company values
  values = [
    {
      icon: '🤝',
      title: 'Client Partnership',
      description: 'We are more than a vendor; we are a long-term partner. We succeed only when you do.'
    },
    {
      icon: '⚙️',
      title: 'Pragmatic Innovation',
      description: 'We don\'t chase trends. We adopt and build technology that delivers real, measurable results for your operations.'
    },
    {
      icon: '🛡️',
      title: 'Unwavering Integrity',
      description: 'From our code to our contracts, we operate with complete transparency and honesty. Your trust is our most valuable asset.'
    },
    {
      icon: '🔍',
      title: 'Deep Domain Expertise',
      description: 'We believe you can\'t build the best solution without understanding the business inside and out. We are industry experts first, technologists second.'
    }
  ];

  // Leadership team
  leadership = [
    {
      name: '[Founder\'s Name]',
      title: 'Founder & CEO',
      photo: '👨‍💼',
      description: 'Visionary leader who founded the company in 1998 with a mission to solve real-world operational challenges through innovative software solutions.',
      linkedin: '#'
    },
    {
      name: '[Name]',
      title: 'Head of Development',
      photo: '👩‍💻',
      description: 'With 15 years in enterprise software, [Name] ensures that every line of code we ship is secure, scalable, and reliable.',
      linkedin: '#'
    },
    {
      name: '[Name]',
      title: 'Head of Support',
      photo: '👨‍🔧',
      description: 'Leading our client success initiatives, ensuring every customer receives the support they need to maximize their software investment.',
      linkedin: '#'
    }
  ];

  // Team members
  teamMembers = [
    { name: 'Priya', title: 'Senior .NET Developer', photo: '👩‍💻' },
    { name: 'Rohan', title: 'Dairy Solutions Specialist', photo: '👨‍💼' },
    { name: 'Anjali', title: 'Business Analyst', photo: '👩‍🔬' },
    { name: 'Vikram', title: 'Sugar Industry Expert', photo: '👨‍💻' }
  ];

  // Benefits
  benefits = [
    { icon: '💰', title: 'Competitive Salary' },
    { icon: '🏥', title: 'Health Insurance' },
    { icon: '🏖️', title: 'Paid Time Off' },
    { icon: '📚', title: 'Professional Development' },
    { icon: '🎉', title: 'Team Outings & Events' },
    { icon: '🤝', title: 'Collaborative Environment' }
  ];

  // Job openings
  jobOpenings = [
    {
      title: 'Senior C# Developer',
      location: 'Kolhapur, India',
      type: 'Full-time',
      description: 'Join our development team to build robust ERP solutions for dairy, sugar, and jewelry industries.'
    },
    {
      title: 'Business Analyst - Dairy Industry',
      location: 'Kolhapur, India',
      type: 'Full-time',
      description: 'Work closely with clients to understand requirements and translate them into technical specifications.'
    },
    {
      title: 'Software Support Specialist',
      location: 'Kolhapur, India',
      type: 'Full-time',
      description: 'Provide technical support and training to our clients, ensuring they get maximum value from our solutions.'
    }
  ];
  

  private intersectionObserver!: IntersectionObserver;

  constructor() {}

  ngOnInit(): void {
    console.log(this.activeTab + ' tab is active');
    this.setupIntersectionObserver();
  }

  ngAfterViewInit(): void {
    this.observeElements();
  }

  ngOnDestroy(): void {
    if (this.intersectionObserver) {
      this.intersectionObserver.disconnect();
    }
  }

  // Tab navigation
  switchTab(tabId: string): void {
    this.activeTab = tabId;
  }

  isActiveTab(tabId: string): boolean {
    return this.activeTab === tabId;
  }

  // Intersection Observer for scroll animations
  private setupIntersectionObserver(): void {
    const options = {
      threshold: 0.1,
      rootMargin: '0px 0px -50px 0px'
    };

    this.intersectionObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const element = entry.target as HTMLElement;
          element.style.opacity = '1';
          element.style.transform = 'translateY(0)';
        }
      });
    }, options);
  }

  private observeElements(): void {
    const elements = document.querySelectorAll('.timeline-item, .value-card, .team-member, .benefit-item, .job-item');
    
    
    elements.forEach(el => {
      const htmlEl = el as HTMLElement;
      htmlEl.style.opacity = '0';
      htmlEl.style.transform = 'translateY(30px)';
      htmlEl.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
      this.intersectionObserver.observe(el);
    });
  }

  // Smooth scroll utility
  scrollToSection(sectionId: string): void {
    const element = document.getElementById(sectionId);
    if (element) {
      element.scrollIntoView({
        behavior: 'smooth',
        block: 'start'
      });
    }
  }

  // Job application handler
  applyForJob(jobTitle: string): void {
    // Implement job application logic
    console.log(`Applying for: ${jobTitle}`);
    // You can open a modal, navigate to application form, etc.
  }

  // Contact form handler
  contactUs(): void {
    // Implement contact logic
    console.log('Contact us clicked');
    // Navigate to contact page or open contact modal
  }

  // Utility method for tracking by index in *ngFor
  trackByIndex(index: number, item: any): number {
    return index;
  }

  trackByTitle(index: number, item: any): string {
    return item.title || item.name || index;
  }
  
}