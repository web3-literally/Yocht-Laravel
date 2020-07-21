<?php

########################################################################################################################
# Dashboard ############################################################################################################
########################################################################################################################

Breadcrumbs::for('dashboard', function ($trail) {
    $trail->push(trans('general.dashboard'), route('dashboard'));
});

Breadcrumbs::for('account.dashboard.related-to', function ($trail) {
    $related_member_id = request()->route('related_member_id');
    if ($member = \App\User::find($related_member_id)) {
        $trail->push($member->full_name);
        if ($member->isBusinessAccount()) {
            $trail->push(trans('general.business_dashboard'), route('account.dashboard'));
        }
        if ($member->isBoatAccount()) {
            $trail->push(trans('general.vessel_dashboard'), route('account.dashboard'));
        }
    } else {
        $trail->push(trans('general.dashboard'), route('account.dashboard'));
    }
});

Breadcrumbs::for('account.dashboard', function ($trail) {
    $trail->parent('account.dashboard.related-to');
});

########################################################################################################################

# Events
Breadcrumbs::for('account.events.index', function ($trail, $related_member_id) {
    $trail->parent('account.dashboard.related-to');
    $trail->push(trans('events.events'), route('account.events.index'));
});
Breadcrumbs::for('account.events.create', function ($trail, $related_member_id) {
    $trail->parent('account.events.index', $related_member_id);
    $trail->push(trans('events.new_event'));
});
Breadcrumbs::for('account.events.edit', function ($trail, $related_member_id) {
    $trail->parent('account.events.index', $related_member_id);
    $trail->push(trans('events.edit_event'));
});
# End Events

# Accounts
Breadcrumbs::for('accounts.index', function ($trail) {
    $trail->parent('account.dashboard');
    $trail->push(trans('accounts.accounts'), route('accounts.index'));
});
Breadcrumbs::for('accounts.profile.contact', function ($trail, $user_id) {
    $trail->parent('accounts.index');
    if ($user = App\User::find($user_id)) {
        $trail->push($user->member_title);
    }
    $trail->push(trans('general.account_contact'));
});
Breadcrumbs::for('accounts.profile.photo', function ($trail, $user_id) {
    $trail->parent('accounts.index');
    if ($user = App\User::find($user_id)) {
        $trail->push($user->member_title);
    }
    $trail->push(trans('general.account_photo'));
});
Breadcrumbs::for('accounts.profile.newsletter', function ($trail, $user_id) {
    $trail->parent('accounts.index');
    if ($user = App\User::find($user_id)) {
        $trail->push($user->member_title);
    }
    $trail->push(trans('general.account_newsletter'));
});
# End Accounts

# Businesses
Breadcrumbs::for('account.businesses', function ($trail) {
    $trail->parent('account.dashboard');
    $trail->push(trans('businesses.businesses'), route('account.businesses'));
});
Breadcrumbs::for('account.businesses.add', function ($trail) {
    $trail->parent('account.businesses');
    $trail->push(trans('businesses.add_business'));
});
Breadcrumbs::for('account.businesses.profile.details', function ($trail, $business_id) {
    $trail->parent('account.businesses');
    if ($business = \App\Models\Business\Business::find($business_id)) {
        $trail->push($business->name);
    }
    $trail->push(trans('general.details'));
});
Breadcrumbs::for('account.businesses.profile.listing', function ($trail, $business_id) {
    $trail->parent('account.businesses');
    if ($business = \App\Models\Business\Business::find($business_id)) {
        $trail->push($business->name);
    }
    $trail->push(trans('general.account_listing_details'));
});
Breadcrumbs::for('account.businesses.profile.photo', function ($trail, $business_id) {
    $trail->parent('account.businesses');
    if ($business = \App\Models\Business\Business::find($business_id)) {
        $trail->push($business->name);
    }
    $trail->push(trans('general.account_photo'));
});
Breadcrumbs::for('account.businesses.profile.video', function ($trail, $business_id) {
    $trail->parent('account.businesses');
    if ($business = \App\Models\Business\Business::find($business_id)) {
        $trail->push($business->name);
    }
    $trail->push(trans('general.account_video'));
});
Breadcrumbs::for('account.businesses.profile.about', function ($trail, $business_id) {
    $trail->parent('account.businesses');
    if ($business = \App\Models\Business\Business::find($business_id)) {
        $trail->push($business->name);
    }
    $trail->push(trans('general.account_about'));
});
Breadcrumbs::for('account.businesses.profile.services', function ($trail, $business_id) {
    $trail->parent('account.businesses');
    if ($business = \App\Models\Business\Business::find($business_id)) {
        $trail->push($business->name);
    }
    $trail->push(trans('general.business_categories'));
});
Breadcrumbs::for('account.businesses.profile.service-areas', function ($trail, $business_id) {
    $trail->parent('account.businesses');
    if ($business = \App\Models\Business\Business::find($business_id)) {
        $trail->push($business->name);
    }
    $trail->push(trans('general.service_areas'));
});
# Employees
Breadcrumbs::for('account.businesses.employees.index', function ($trail, $business_id) {
    $trail->parent('account.businesses');
    if ($business = \App\Models\Business\Business::find($business_id)) {
        $trail->push($business->name);
    }
    $trail->push(trans('employees.employees'), route('account.businesses.employees.index', ['business_id' => $business_id]));
});
Breadcrumbs::for('account.businesses.employees.assign', function ($trail, $business_id) {
    $trail->parent('account.businesses.employees.index', $business_id);
    $trail->push(trans('employees.assign_member'));
});
Breadcrumbs::for('account.businesses.employees.profile.contact', function ($trail, $business_id, $user_id) {
    $trail->parent('account.businesses.employees.index', $business_id);
    if ($user = \App\Employee::find($user_id)) {
        $trail->push($user->full_name);
    }
    $trail->push(trans('general.account_contact'));
});
Breadcrumbs::for('account.businesses.employees.profile.photo', function ($trail, $business_id, $user_id) {
    $trail->parent('account.businesses.employees.index', $business_id);
    if ($user = \App\Employee::find($user_id)) {
        $trail->push($user->full_name);
    }
    $trail->push(trans('general.account_photo'));
});
# End Businesses

# Boats (Vessel / Tender)
Breadcrumbs::for('account.vessels', function ($trail) {
    $trail->parent('account.dashboard');
    $trail->push(trans('vessels.vessels'), route('account.vessels'));
});
Breadcrumbs::for('account.vessels.add', function ($trail) {
    $trail->parent('account.vessels');
    $trail->push(trans('vessels.add_vessel'), route('account.vessels.add'));
});
Breadcrumbs::for('account.tenders.add', function ($trail) {
    $trail->parent('account.vessels');
    $trail->push(trans('vessels.add_tender'), route('account.tenders.add'));
});
Breadcrumbs::for('account.vessels.edit', function ($trail) {
    $trail->parent('account.vessels');
    $trail->push(trans('vessels.edit_vessel'));
});
Breadcrumbs::for('account.tenders.edit', function ($trail) {
    $trail->parent('account.vessels');
    $trail->push(trans('vessels.edit_tender'));
});
Breadcrumbs::for('account.vessels.profile', function ($trail, $boat_id) {
    $trail->parent('account.boat', $boat_id);
    $trail->push(trans('general.manage_profile'));
});
Breadcrumbs::for('account.vessels.profile.details', function ($trail, $boat_id) {
    $trail->parent('account.vessels.profile', $boat_id);
});
Breadcrumbs::for('account.vessels.profile.attachments', function ($trail, $boat_id) {
    $trail->parent('account.vessels.profile', $boat_id);
    $trail->push(trans('general.attachments'));
});
Breadcrumbs::for('account.vessels.profile.video', function ($trail, $boat_id) {
    $trail->parent('account.vessels.profile', $boat_id);
    $trail->push(trans('general.video'));
});
Breadcrumbs::for('account.vessels.profile.about', function ($trail, $boat_id) {
    $trail->parent('account.vessels.profile', $boat_id);
    $trail->push(trans('general.account_about'));
});
Breadcrumbs::for('account.tenders.profile', function ($trail, $boat_id) {
    $trail->parent('account.boat', $boat_id);
    $trail->push(trans('general.manage_profile'));
});
# Boat Transfer
Breadcrumbs::for('account.boat.transfer.step', function ($trail, $boat_id, $step) {
    $trail->parent('account.boat', $boat_id);
    $trail->push(trans('vessels.transfer.transfer'), route('account.boat.transfer.step', ['boat_id' => $boat_id, 'step' => $step]));
});
Breadcrumbs::for('account.boat.transfer.details', function ($trail, $boat_id, $transfer_id) {
    $trail->parent('account.vessels');
    if ($boat = \App\Models\Vessels\Vessel::find($boat_id)) {
        $trail->push($boat->name);
    }
    $trail->push(trans('vessels.transfer.details'), route('account.boat.transfer.details', ['boat_id' => $boat_id, 'transfer_id' => $transfer_id]));
});
Breadcrumbs::for('account.boat.transfer.origin_confirm', function ($trail, $transfer_id) {
    $trail->parent('home');
    $trail->push(trans('vessels.transfer.confirm_transfer'));
});
# Boat Crew
Breadcrumbs::for('account.boat.crew.index', function ($trail) {
    $trail->parent('account.dashboard.related-to');
    $trail->push(trans('vessels.crew'), route('account.boat.crew.index'));
});
Breadcrumbs::for('account.boat.crew.create', function ($trail) {
    $trail->parent('account.boat.crew.index');
    $trail->push(trans('crew.create_member'), route('account.boat.crew.create'));
});
# Boat Jobs
Breadcrumbs::for('account.jobs.index', function ($trail) {
    $trail->parent('account.dashboard.related-to');
    $trail->push(trans('jobs.jobs'), route('account.jobs.index'));
});
Breadcrumbs::for('account.jobs.edit', function ($trail) {
    $trail->parent('account.jobs.index');
    $trail->push(trans('jobs.edit_job'));
});
Breadcrumbs::for('account.jobs.complete', function ($trail, $related_member_id, $id) {
    $trail->parent('account.jobs.index');
    if ($job = \App\Models\Jobs\Job::find($id)) {
        $trail->push($job->title);
    }
    $trail->push(trans('jobs.job_completed'));
});
Breadcrumbs::for('account.jobs.tickets', function ($trail) {
    $trail->parent('account.jobs.index');
    $trail->push(trans('jobs.job_applications'));
});
Breadcrumbs::for('account.jobs.applications', function ($trail, $ticket_id, $id) {
    $trail->parent('account.jobs.tickets');
    $trail->push($id, route('account.jobs.applications', ['id' => $id]));
});
Breadcrumbs::for('account.jobs.applicant.messages', function ($trail, $ticket_id, $id) {
    $trail->parent('account.jobs.applications', $ticket_id, $id);
    $trail->push(trans('message.messages'));
});
Breadcrumbs::for('account.jobs.related', function ($trail, $memberId) {
    $trail->parent('dashboard');
    $trail->push(trans('jobs.jobs'));
    $trail->push(trans('jobs.related'));
    if ($member = \App\User::findOrFail($memberId)) {
        $trail->push($member->member_title);
    }
});
Breadcrumbs::for('account.jobs.applications.ask-details', function ($trail, $related_member_id, $id, $user) {
    $trail->parent('account.jobs.applications', $related_member_id, $id);
    $trail->push(trans('jobs.choose_user'));
    $trail->push(trans('jobs.fill_job_details'));
});
# Job wizard
Breadcrumbs::for('account.jobs.wizard.members', function ($trail) {
    $trail->parent('account.dashboard.related-to');
    $trail->push(trans('jobs.jobs'), route('account.jobs.index'));
    $trail->push(trans('jobs.members'));
});
Breadcrumbs::for('account.jobs.wizard.period', function ($trail) {
    $trail->parent('account.jobs.wizard.members');
    $trail->push(trans('jobs.shipyard_period'));
});
Breadcrumbs::for('account.jobs.wizard.job', function ($trail) {
    $trail->parent('account.jobs.wizard.members');
    $trail->push(trans('jobs.new_job'));
});
# End Boats (Vessel / Tender)

# Documents
Breadcrumbs::for('account.documents.index', function ($trail) {
    $trail->parent('account.dashboard.related-to');
    $trail->push(trans('general.documents_storage'), route('account.documents.index'));
});
Breadcrumbs::for('account.templates.index', function ($trail) {
    $trail->parent('account.dashboard.related-to');
    $trail->push(trans('general.template_documents_storage'), route('account.templates.index'));
});

# Crew
Breadcrumbs::for('account.crew.index', function ($trail) {
    $trail->parent('dashboard');
    $trail->push(trans('crew.crew'));
});
Breadcrumbs::for('account.crew.profile.contact', function ($trail, $user_id) {
    $trail->parent('account.crew.index');
    if ($user = App\User::find($user_id)) {
        $trail->push($user->member_title);
    }
    $trail->push(trans('general.account_contact'));
});
Breadcrumbs::for('account.crew.profile.photo', function ($trail, $user_id) {
    $trail->parent('account.crew.index');
    if ($user = App\User::find($user_id)) {
        $trail->push($user->member_title);
    }
    $trail->push(trans('general.account_photo'));
});
Breadcrumbs::for('account.crew.profile.newsletter', function ($trail, $user_id) {
    $trail->parent('account.crew.index');
    if ($user = App\User::find($user_id)) {
        $trail->push($user->member_title);
    }
    $trail->push(trans('general.account_newsletter'));
});
# End Crew

# Tickets
Breadcrumbs::for('account.tickets.index', function ($trail) {
    $trail->parent('account.dashboard');
    $trail->push(trans('tickets.tickets'), route('account.tickets.index'));
});
Breadcrumbs::for('account.tickets.details', function ($trail, $id) {
    $trail->parent('account.tickets.index');
    $trail->push($id);
});
Breadcrumbs::for('account.tickets.messages', function ($trail, $id) {
    $trail->parent('account.tickets.details', $id);
    $trail->push(trans('message.messages'));
});
Breadcrumbs::for('account.tickets.related', function ($trail, $memberId) {
    $trail->parent('account.tickets.index');
    $trail->push(trans('tickets.related'));
    if ($member = \App\User::findOrFail($memberId)) {
        $trail->push($member->member_title);
    }
});
# End Tickets

# Classifieds
Breadcrumbs::for('classifieds.index', function ($trail) {
    $trail->parent('account.dashboard');
    $trail->push(trans('classifieds.classifieds'), route('classifieds.index'));
});
Breadcrumbs::for('classifieds.create', function ($trail) {
    $trail->parent('classifieds.index');
    $trail->push(trans('classifieds.new_classified'));
});
Breadcrumbs::for('classifieds.edit', function ($trail) {
    $trail->parent('classifieds.index');
    $trail->push(trans('classifieds.edit_classified'));
});
Breadcrumbs::for('classifieds.refresh', function ($trail, $id) {
    $trail->parent('home');
    $trail->push(trans('classifieds.classifieds'));
});
Breadcrumbs::for('classifieds.deactivate', function ($trail, $id) {
    $trail->parent('home');
    $trail->push(trans('classifieds.classifieds'));
});
# End Classifieds

# Tasks
Breadcrumbs::for('account.tasks.index', function ($trail) {
    $trail->parent('account.dashboard.related-to');
    $trail->push(\App\Helpers\Tasks::getTaskManagerTitle(), route('account.tasks.index'));
});
Breadcrumbs::for('account.tasks.create', function ($trail, $related_member_id) {
    $trail->parent('account.tasks.index');
    $trail->push(trans('tasks.new_task'));
});
Breadcrumbs::for('account.tasks.edit', function ($trail, $related_member_id, $id) {
    $trail->parent('account.tasks.index');
    if ($model = \App\Models\Tasks\Task::findOrFail($id)) {
        $trail->push($model->title);
    }
    $trail->push(trans('tasks.edit_task'));
});
Breadcrumbs::for('account.tasks.repeat', function ($trail, $related_member_id, $id) {
    $trail->parent('account.tasks.index');
    if ($model = \App\Models\Tasks\Task::findOrFail($id)) {
        $trail->push($model->title);
    }
    $trail->push(trans('tasks.repeat_task'));
});
# End Tasks

Breadcrumbs::for('my-profile', function ($trail) {
    $trail->parent('account.overview');
    $trail->push(trans('general.profile'), route('my-profile'));
});

Breadcrumbs::for('profile.contact', function ($trail) {
    $trail->parent('my-profile');
    $trail->push(trans('general.account_contact'), route('profile.contact'));
});
Breadcrumbs::for('profile.photo', function ($trail) {
    $trail->parent('my-profile');
    $trail->push(trans('general.account_photo'), route('profile.photo'));
});
Breadcrumbs::for('profile.video', function ($trail) {
    $trail->parent('my-profile');
    $trail->push(trans('general.account_video'), route('profile.video'));
});
Breadcrumbs::for('profile.newsletter', function ($trail) {
    $trail->parent('my-profile');
    $trail->push(trans('general.account_newsletter'), route('profile.newsletter'));
});

# Billing
Breadcrumbs::for('payment-methods', function ($trail) {
    $trail->parent('account.overview');
    $trail->push(trans('billing.payment_methods'), route('payment-methods'));
});
Breadcrumbs::for('payment-methods.add', function ($trail) {
    $trail->parent('payment-methods');
    $trail->push(trans('billing.add_payment_method'));
});
Breadcrumbs::for('subscriptions', function ($trail) {
    $trail->parent('account.overview');
    $trail->push(trans('billing.subscriptions'), route('subscriptions'));
});
Breadcrumbs::for('invoices', function ($trail) {
    $trail->parent('account.overview');
    $trail->push(trans('billing.invoices'), route('invoices'));
});
# End Billing

Breadcrumbs::for('account.overview', function ($trail) {
    $trail->push(trans('general.overview'), route('account.overview'));
});

Breadcrumbs::for('account.change-password', function ($trail) {
    $trail->parent('account.overview');
    $trail->push(trans('general.account_change_password'), route('account.change-password'));
});

Breadcrumbs::for('account.notifications.index', function ($trail) {
    $trail->parent('account.dashboard');
    $trail->push(trans('notification.notifications'), route('account.notifications.index'));
});

Breadcrumbs::for('account.related.notifications.index', function ($trail) {
    $trail->parent('account.dashboard');
    $trail->push(trans('notification.notifications'), route('account.related.notifications.index'));
});

Breadcrumbs::for('account.messages.index', function ($trail) {
    $trail->parent('account.dashboard');
    $trail->push(trans('message.messages'), route('account.messages.index'));
});
Breadcrumbs::for('account.messages.show', function ($trail, $id) {
    $trail->parent('account.messages.index');
    if ($thread = \App\Models\Messenger\Thread::find($id)) {
        $trail->push(trans('message.messages_with', ['name' => $thread->directUser()->full_name]), route('account.messages.show', $id));
    }
});
Breadcrumbs::for('account.messages.create', function ($trail, $member_id) {
    $trail->parent('account.messages.index');
    if ($member = \App\User::findOrFail($member_id)) {
        $trail->push(trans('message.contact_to', ['name' => $member->full_name]), route('account.messages.create', $member_id));
    }
});

Breadcrumbs::for('account.related.messages.index', function ($trail) {
    $trail->parent('account.dashboard');
    $trail->push(trans('message.messages'), route('account.related.messages.index'));
});
Breadcrumbs::for('account.related.messages.show', function ($trail, $related_member_id, $id) {
    $trail->parent('account.messages.index');
    if ($thread = \App\Models\Messenger\Thread::find($id)) {
        $trail->push(trans('message.messages_with', ['name' => $thread->directUser()->full_name]), route('account.related.messages.show', $id));
    }
});

# Services
/*Breadcrumbs::for('dashboard.services.index', function ($trail) {
    $trail->parent('dashboard');
    $trail->push(trans('services.services'), route('dashboard.services.index'));
});
Breadcrumbs::for('dashboard.services.category', function ($trail, $category_id) {
    $trail->push(trans('services.services'), route('dashboard.services.index'));
    if ($category = \App\Models\Services\ServiceCategory::find($category_id)) {
        $trail->push($category->label, route('dashboard.services.category', ['category_id' => $category->id]));
    }
});
Breadcrumbs::for('dashboard.services.service', function ($trail, $category_id, $slug) {
    $trail->parent('dashboard.services.category', $category_id);
    if ($service = \App\Models\Services\Service::findBySlug($slug)) {
        $trail->push($service->title, route('dashboard.services.service', ['category_id' => $category_id, 'slug' => $service->slug]));
    }
});*/
# End Services

########################################################################################################################
# Site #################################################################################################################
########################################################################################################################

Breadcrumbs::for('home', function ($trail) {
    $trail->push('Home', route('home'));
});

# Classifieds
Breadcrumbs::for('classifieds', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('general.classifieds'), route('classifieds'));
});
Breadcrumbs::for('classifieds.category', function ($trail, $slug) {
    $trail->parent('classifieds');
    if ($category = \App\Models\Classifieds\ClassifiedsCategory::findBySlug($slug)) {
        $trail->push($category->title, route('classifieds.category', $category->slug));
    }
});
Breadcrumbs::for('classifieds.find', function ($trail, $type) {
    $trail->parent('classifieds');
    $trail->push('Search');
    $types = \App\Models\Classifieds\Classifieds::getTypes();
    if (isset($types[$type])) {
        $trail->push($types[$type]);
    }
});
Breadcrumbs::for('classifieds.filter', function ($trail, $type) {
    $trail->parent('classifieds');
    $trail->push('Search');
    $types = \App\Models\Classifieds\Classifieds::getTypes();
    if (isset($types[$type])) {
        $trail->push($types[$type]);
    }
});
Breadcrumbs::for('classifieds.show', function ($trail, $category_slug, $slug) {
    $trail->parent('classifieds');
    if ($classified = \App\Models\Classifieds\Classifieds::findBySlug($slug)) {
        $trail->push($classified->category->title, route('classifieds.category', ['type' => $classified->type, $classified->category->slug]));
        $trail->push($classified->title, route('classifieds.show', ['slug' => $slug, 'category_slug' => $classified->category->slug]));
    }
});
# End Classifieds

# News
Breadcrumbs::for('news', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('general.news'), route('news'));
});
Breadcrumbs::for('news.show', function ($trail, $year, $month, $slug) {
    $trail->parent('news');
    if ($model = \App\Models\News::findBySlug($slug)) {
        $trail->push($model->title);
    }
});
# End News

# Blog
Breadcrumbs::for('blog', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('general.blog'), route('blog'));
});
Breadcrumbs::for('blog-post', function ($trail, $category, $slug) {
    $trail->parent('blog');
    if ($blog = \App\Blog::findBySlug($slug)) {
        $trail->push($blog->title, route('blog-post', ['category' => $category, 'slug' => $slug]));
    }
});
# End Blog

# Events
Breadcrumbs::for('events', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('events.events'), route('events'));
});
Breadcrumbs::for('events.show', function ($trail, $slug) {
    $trail->parent('events');
    if ($event = \App\Models\Events\Event::findBySlug($slug)) {
        $trail->push($event->title, route('events.show', $slug));
    }
});
# End Events

# Jobs
Breadcrumbs::for('jobs', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('jobs.jobs'), route('jobs'));
});
/*Breadcrumbs::for('jobs.search', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('jobs.jobs'), route('jobs.search'));
});
Breadcrumbs::for('jobs.results', function ($trail) {
    $trail->parent('jobs.search');
    $trail->push(trans('jobs.search'));
    $trail->push(trans('jobs.results'), route('jobs.results'));
});*/
Breadcrumbs::for('jobs.published', function ($trail) {
    $trail->parent('jobs');
    $trail->push(trans('jobs.published'), route('jobs.published'));
});
Breadcrumbs::for('jobs.in_process', function ($trail) {
    $trail->parent('jobs');
    $trail->push(trans('jobs.in_process'), route('jobs.in_process'));
});
Breadcrumbs::for('jobs.completed', function ($trail) {
    $trail->parent('jobs');
    $trail->push(trans('jobs.completed'), route('jobs.completed'));
});
Breadcrumbs::for('jobs.show', function ($trail, $slug) {
    $trail->parent('jobs');
    if ($job = App\Models\Jobs\Job::findBySlug($slug)) {
        $trail->push($job->title, route('jobs.show', $slug));
    }
});
Breadcrumbs::for('jobs.show.private', function ($trail, $slug, $related_id) {
    $trail->parent('jobs');
    if ($job = App\Models\Jobs\Job::findBySlug($slug)) {
        $trail->push($job->title, route('jobs.show.private', ['related_id' => $related_id, 'slug' => $slug]));
    }
});
Breadcrumbs::for('jobs.apply-form', function ($trail, $slug) {
    $trail->parent('jobs');
    if ($job = App\Models\Jobs\Job::findBySlug($slug)) {
        $trail->push($job->title, route('jobs.show', $slug));
    }
    $trail->push(trans('jobs.apply_for_this_job'));
});
Breadcrumbs::for('jobs.review', function ($trail, $id) {
    $trail->parent('jobs');
    if ($job = App\Models\Jobs\Job::find($id)) {
        $trail->push($job->user->member_title, route('members.show', $job->user->id));
        $trail->push($job->title, route('jobs.show', $job->slug));
    }
    $trail->push(trans('reviews.post_a_review'));
});
# End Jobs

Breadcrumbs::for('contact', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('general.contact'), route('contact'));
});

Breadcrumbs::for('about-us', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('general.about_us'), route('about-us'));
});

# Our Team
Breadcrumbs::for('our-team', function ($trail) {
    $trail->parent('about-us');
    $trail->push(trans('general.our_team'), route('our-team'));
});
Breadcrumbs::for('team-member', function ($trail, $id) {
    $trail->parent('our-team');
    $trail->push('Jonathan Barnes', route('team-member', $id));
});
# End Our Team

# SignUp
Breadcrumbs::for('signup', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('general.sign_up'), route('signup'));
});
Breadcrumbs::for('signup.owner-vessel-account', function ($trail) {
    $trail->parent('signup');
    $trail->push(trans('general.vessel_signup'));
});
Breadcrumbs::for('signup.owner-transfer-account', function ($trail) {
    $trail->parent('signup');
    $trail->push(trans('general.transfer_signup'));
});
Breadcrumbs::for('signup.owner-account.vessel-info', function ($trail) {
    $trail->parent('signup.owner-vessel-account');
    $trail->push(trans('general.vessel_info'));
});
Breadcrumbs::for('signup.owner-account.payment-info', function ($trail) {
    $trail->parent('signup.transfer-owner-account');
    $trail->push(trans('general.payment_info'));
});

Breadcrumbs::for('signup.owner-marine-contractor-account', function ($trail) {
    $trail->parent('signup');
    $trail->push(trans('general.marine_contractor'));
});
Breadcrumbs::for('signup.owner-marine-contractor-account.business-info', function ($trail) {
    $trail->parent('signup.owner-marine-contractor-account');
    $trail->push(trans('general.business_info'));
});
Breadcrumbs::for('signup.owner-marine-contractor-account.payment-info', function ($trail) {
    $trail->parent('signup.owner-marine-contractor-account');
    $trail->push(trans('general.payment_info'));
});

Breadcrumbs::for('signup.owner-marinas-shipyards-account', function ($trail) {
    $trail->parent('signup');
    $trail->push(trans('general.marinas_shipyards'));
});
Breadcrumbs::for('signup.owner-marinas-shipyards-account.business-info', function ($trail) {
    $trail->parent('signup.owner-marinas-shipyards-account');
    $trail->push(trans('general.business_info'));
});

Breadcrumbs::for('signup.owner-land-services-account', function ($trail) {
    $trail->parent('signup');
    $trail->push(trans('general.land_services'));
});
Breadcrumbs::for('signup.owner-land-services-account.business-info', function ($trail) {
    $trail->parent('signup.owner-land-services-account');
    $trail->push(trans('general.business_info'));
});

Breadcrumbs::for('subscription.plans', function ($trail) {
    $trail->parent('signup');
    $trail->push(trans('general.member_plan'), route('subscription.plans'));
});
# End SignUp

# Activate Account
Breadcrumbs::for('activate-free-success', function ($trail) {
    $trail->parent('signup');
    $trail->push(trans('general.activate'));
});
Breadcrumbs::for('activate', function ($trail) {
    $trail->parent('signup');
    $trail->push(trans('general.activate'));
});
# End Activate Account

Breadcrumbs::for('forgot-password-success', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('general.forgot_password'));
});
Breadcrumbs::for('forgot-password-confirm', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('general.forgot_password'));
});

Breadcrumbs::for('site-page', function ($trail, $name) {
    $trail->parent('home');
    if ($page = \App\Page::findBySlug($name)) {
        $trail->push($page->title, route('site-page', $name));
    }
});

# Search
Breadcrumbs::for('search', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('general.search'));
});
# End Search

# Members
Breadcrumbs::for('members.index', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('general.members'), route('members.index'));
});
Breadcrumbs::for('members.show', function ($trail, $id) {
    $trail->parent('members.index');
    if ($member = \App\User::searchableAccounts()->find($id)) {
        $trail->push($member->member_title, route('members.show', $member->id));
    }
});
Breadcrumbs::for('members.business.show', function ($trail, $id) {
    $trail->parent('members.index');
    $trail->push(trans('general.businesses'));
    if ($member = \App\User::searchableAccounts()->find($id)) {
        $trail->push($member->member_title, route('members.business.show', $member->id));
    }
});
Breadcrumbs::for('members.vessel.show', function ($trail, $id) {
    $trail->parent('members.index');
    $trail->push(trans('general.vessels'));
    if ($member = \App\User::searchableAccounts()->find($id)) {
        $trail->push($member->member_title, route('members.vessel.show', $member->id));
    }
});
Breadcrumbs::for('members.contact-to', function ($trail, $id) {
    $trail->parent('members.show', $id);
    $trail->push(trans('message.send_message'));
});
Breadcrumbs::for('members.review', function ($trail, $id) {
    $trail->parent('members.show', $id);
    $trail->push(trans('reviews.post_a_review'));
});
Breadcrumbs::for('members.reviews', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('general.member_reviews'));
});
Breadcrumbs::for('members.categories', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('general.members_by_category'));
});
# End Members

# Reviews
Breadcrumbs::for('reviews.show', function ($trail, $id) {
    $trail->parent('home');
    $trail->push(trans('reviews.reviews'), route('members.reviews'));
    if ($review = \App\Models\Reviews\Review::find($id)) {
        if ($review->for->for == 'member') {
            $trail->push($review->for->instance->member_title, route('members.show', $review->for->instance->id));
            $trail->push($review->title, route('reviews.show', $id));
        } else {
            $trail->push($review->title, route('reviews.show', $id));
        }
    }
});
Breadcrumbs::for('reviews.set-status', function ($trail, $id) {
    $trail->parent('home');
    $trail->push(trans('reviews.reviews'));
});
# End Reviews

# Manufacturers
Breadcrumbs::for('manufacturers.set-status', function ($trail, $id) {
    $trail->parent('home');
    $trail->push(trans('vessels.manufacturers'));
});
# End Manufacturers

# Favorites
Breadcrumbs::for('favorites.members.index', function ($trail) {
    $trail->parent('dashboard');
    $trail->push(trans('general.some_favorite', ['some' => trans('general.members')]), route('favorites.members.index'));
});
Breadcrumbs::for('favorites.classifieds.index', function ($trail) {
    $trail->parent('dashboard');
    $trail->push(trans('general.some_favorite', ['some' => trans('classifieds.classifieds')]), route('favorites.classifieds.index'));
});
Breadcrumbs::for('favorites.jobs.index', function ($trail) {
    $trail->parent('dashboard');
    $trail->push(trans('general.some_favorite', ['some' => trans('jobs.jobs')]), route('favorites.jobs.index'));
});
Breadcrumbs::for('favorites.events.index', function ($trail) {
    $trail->parent('dashboard');
    $trail->push(trans('general.some_favorite', ['some' => trans('events.events')]), route('favorites.events.index'));
});
# End Favorites

# Complete missed profile information
Breadcrumbs::for('complete.profile.index', function ($trail) {
    $trail->parent('dashboard');
    $trail->push(trans('general.account_fill_profile'));
});
# End Complete missed profile information
