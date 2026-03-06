gender ONLY male female.

users (alter) — add: slug, first_name, last_name, phone, locale (default 'sk'), date_started_working_out, journey_text (json),
journey_image, main_image, socials (json), country_code, date_started_coaching, biography (json), main_background_image, biography_image,
contact_email, contact_phone - is this valid? Like dont we rather add this to some other tables like "athletes" or "coaches"

The teams logic. Is it default team and multitenancy logic as filament default? I know filament is shipped with multitenancy by default. What is the difference? can we use it?

Do we need translated slugs? Like i am thinking its better to have .com domain and just change path to /cz/path... or /en/path... default would not need be set at all, that would go to sk locale /path.

Teams can have everything scoped to team. Sport categories, exercises (but can use existing ones from BCZ (default) team), athletes, coaches, certifications, but for example competitions dont need to be, they are existing and can be reused among teams.
exhibition categories? you meant event categories right?

I noticed we store many data like training_registration in json, is it really good idea? couldnt we rather make some hasMany like "column_name", "value" table?

Competitions are more robust than one would think:

We either organize competition, or just share external link to competition without registration and organization stuff.
Athletes of teams can write reports from specific competitions, where they can show results, winners, gallery... this is similar to event resource, but scoped just for competitions. But i want all content builders to share all possible sections.
Competitions will have these data:
- date from, date to, place of competition, format of competition (competition format id)
- athlete categories of competition (competition athlete category id)
- status (registration open, registration closed, in_progress (but this one could be logically derived), finished).
- organizer (team id)

Competition will have some description, harmonogram (program of the day, lets say timetable)
- Timetable might look like this: 
- 8:00 registration + weight in
- 9:00 qualification round
- 12:00 lunch break
- 13:00 finals
- 15:00 publishing results

Competition will give info that if organizer has delays, f.e. 15 minutes, etc...

Athlete competition categories:
- can have inheritance, like:
  - women
    - under 60kg
    - over 60kg
  - men
    - under 80kg
    - over 80kg
  - kids

Each category can have its own competition format, like women can have battles and kids and men can have qualification round (first X goes to second round) + second round as battle bracket. Or only battle bracket. Or only rounds and results by their points. Battles can be one vs one, two vs two etc.. can be dynamic.

Each competition has its disciplines, each has associated judge (user with JUDGE role)
f.e. "statics", "dynamics", "combos".
These disciplines are shared among teams. not need to be scoped.
Disciplines have
- name,
- description
- scoring_description
- icon
- image
- scoring format (points, coach decision (yes or no, no results, just from the vibe))
  - f.e. points, or coach says in battle that he gave his "yes" to either one or second guy.
Each judge can have also his own profile.

Each competition format has to have set system of scoring. It can be represented by some description field.
f.e.

Harmonogram during the event (live) will need to be each part manually started and finished. If not finished yet, but already over time, has to show delays LIVE. (f.e. 1h 15m delayed.).

each competition has to show each round how many athletes goes to second, if not battle bracket. Also there should be system of battle bracket of random defining who goes against who, random shuffle, but also possible to manually change.
Each athlete that registers to a competition is actually a user who is athelete. If not registered yet, automatically created account from the form submission. (notified via email.)
Registration form can be open for public or only for invited users, who have to be logged in only to register.
each registration can have its own fees, in multiple currencies (if battle is set to be international.)
Each competition has its own defined dates, f.e. Registration opened at, Registration closed at, Date of compoetition start, end... Finished competition is shown for public forever, with results.
Also you can set competition so it has visible countdown timer until registration starts. 

One more thing, can we use just for testing some Stripe API, without need to pay for it? Just testing phase.


SuperAdmin - OK
Owner - All but not adds superadmin nor owners.
Admin - All but cant add superadmin nor owners.
Team admin, editor, coach, athlete = SCOPED BY TEAM
Customer - does it need to be scoped actually? But what if customer is also athlete...
Judge - not scoped.
