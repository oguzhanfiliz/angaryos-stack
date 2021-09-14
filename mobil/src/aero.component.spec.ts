import { TestBed, async } from '@angular/core/testing';
import { RouterTestingModule } from '@angular/router/testing';
import { AeroComponent } from './aero.component';

describe('AeroComponent', () => {
  beforeEach(async(() => {
    TestBed.configureTestingModule({
      imports: [
        RouterTestingModule
      ],
      declarations: [
        AeroComponent
      ],
    }).compileComponents();
  }));

  it('should create the app', () => {
    const fixture = TestBed.createComponent(AeroComponent);
    const app = fixture.debugElement.componentInstance;
    expect(app).toBeTruthy();
  });

  it(`should have as title 'light'`, () => {
    const fixture = TestBed.createComponent(AeroComponent);
    const app = fixture.debugElement.componentInstance;
    expect(app.title).toEqual('light');
  });

  it('should render title', () => {
    const fixture = TestBed.createComponent(AeroComponent);
    fixture.detectChanges();
    const compiled = fixture.debugElement.nativeElement;
    expect(compiled.querySelector('.content span').textContent).toContain('light app is running!');
  });
});
