App has three main parts
- competitions (organizing, attending)
- trainings & coaching
- exhibitions - events - lecturing schools

Settings:
- is_exposed - bool
- key - string
- value - json?

Exercise categories:
- name
- description
- sport or training category belongs to many?

Exercises:
- name
- description
- complexity - BASIC, INTERMEDIATE, ADVANCED, ELITE
- image
- category_id
- sport or training category belongs to many?

When user is athlete:
- date_started_working_out,
- journey_text
- journey_image
- main_image
- socials

Athlete can have many "exercises" attached. Like how long did it took him to learn the exercises.
- exercise_id
- duration
- description
- image
- video

Athlete can have many goals.
- icon
- heading
- description

Athlete can also have many "competition" records.
- competition_id
- place (when athlete finishes in 1. place, 2.nd, X. place...)? is this right word for it?
  - should be nullable, because if athlete just attended and didnt finish in the final places, but we wanna record that too.
- category

- Athlete can choose many media from library which he wants to show on his public profile page. videos/images with description. We are gonna use https://filamentphp.com/plugins/ralphjsmit-media-library-manager this paid plugin for filament library. 

# TRAININGS
We have so far two "sports" or training categories
- CALISTHENICS
- PARKOUR

I can imagine these sports as training categories. Each of these categories comes with detailed page that describes the sport. F.e. parkour-freerunning.blade.php is one for parkour, and street-workout.blade.php is one for calisthenics. Its really specific design, but i would like to keep those categories with same structure, so we can manage content on each of those pages.

Training:
for each category we can define group trainings. This will be public group training that our users can join. It is not one time, but holds number of people that attend to it each week. If any of them cancels, place is free for others, and they can join via public form or create their own profile via registration and join via admin dashboard.
we can define age group for each training. f.e. 13 - 17 years old, adults,... or gender, females, males. Also frequency of training, how many per week / month. Duration of training, time of start, place, with also possibility to add exact coords of place of training.
Maximum capacity. Possibility to get notified when training is not full anymore and people can join. Also we want to define place of start, like where we all gather before training.
Each training has multiple or single coaches, some can be main coaches, some can be secondary coaches for helping the main.
Each training can have its gallery chosen from library.
Each training also need to have its own register form, which we can build specifically for each training. SOme need other fields than other. Possible: date picker, year picker, number input, time picker, text input, multiselects, selects, textarea, phone, email, file input, etc...


Coaches:
- each user if has capability of coach can also define his own coach profile.
- coach can have many trainings.
- coach can define his own profile which contains his story / biography, main background image, biography image
- cerftifications and qualifications, with pdf documents possible to upload, from his library. each qualification has name, description, icon, year of issue. His profile also contains section if he is also athlete.
- socials
- contact details
- own gallery.
- date started coaching.
  
Each user, that wants to attend our training, based on the training setting, can access it only by being (do we need member role?) (btw, members should pay membership fee, which is set for each year differently.), or if the training is set to have some price, he has to pay the price first.
Since we will not have possibility to track prices by COD payments, or bank transfer, we will have to have some kind of payment system that we will manually be managing, f.e. someone paid, i add new payment record for this and this training / membership... 
Each team can have its own members, its own payments for membership etc... Also its own settings for payment details etc... We could also do subscription model for this, like monthly/yearly for each team. Team can also be without any members yet. Like we only need the team in database just to mention them or etc.. but dont need to use our app as users yet.

# exhibitions - events - lecturing schools
we have three categories, but can add more later.
- exhibitions
- lecturing schools
- workshops

each category has:
- color representing category
- title
- card_subtitle
- card_description
- card_image
- detail_image
- detail_title
- etc... all that are present in vystupenia.blade.php, prednasky.blade.php and workshopy.blade.php

Each event (or how could we call it? is it valid for this) has:
- title,
- category,
- card_description,
- card_image,
- date
- country
- city
- detail_image,
- detail flexible content, where we can add many blocks, like media, gallery, wysiwyg, blockquote, ul, ol, tables.
- count of attendees,
- client
- Its dedicated gallery that appears always on the bottom of page

Inquiries
represent sent contact forms from exhibitions, events and lectures, but also from contact form that has reasons of contacting us: trainings, workshops, competitions, other
- name
- email
- phone
- message

FAQs:
- categories: color, title
- questions: question, answer.

SPONSORS
- app contains sponsors, can toggle visibility
- sposnor tag like MAIN SPONSOR, MEDIAL SPONSOR, etc...
- logo, link, name, tag.

Users need to have multitenancy. One main team will be BCZ. BCZ team id will be set as default_team_id in settings.
Team can be divided to just team of competition organizers, who can organize their own competitions using our app. So if they dont have any athletes, they dont appear in lists of competing teams.
I want to do also billing, so we can charge users for their usage of our app. for example, some can have free access, and some teams will need to pay for each competition they want to organize. Also can you think of any other possible use case of subscription model for our app?

For all tagging, we might use spatie laravel tags package. https://spatie.be/docs/laravel-tags/v4/introduction https://filamentphp.com/plugins/filament-spatie-tags
 
Just to say: we also need translations for all models and all their fields. I need in filament setting for users to choose their language. Also i need them to be able to change their language via some select. For translating fields, we should use some translation plugin:
https://filamentphp.com/plugins/abdulmajeed-jamaan-translatable-tabs
https://filamentphp.com/plugins/doriiaan-astrotomic
but checkout also their issues on github, if there are any known issues bugs. If possible, we will make custom logic rather than using boxed plugins when we dont need them.

Teams:
Tenancy teams can have public profiles:
- their story/bio
- their achievements
- their gallery
- their socials as a team
- athletes
- coaches

practically everything belongs under a team, but on public we show only our default team athletes, coaches and competitions. do you think thats valid? shouldnt for example exist TEAM_NAME.bcz.com and this kind of stuff? I want it to work under one main domain bcz.
