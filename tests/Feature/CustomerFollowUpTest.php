<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Mail\AdminAlertMail;
use App\Mail\QuotationAcceptedMail;
use App\Models\Comment;
use App\Models\Contact;
use App\Models\Customer;
use App\Models\Project;
use App\Models\Quotation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CustomerFollowUpTest extends TestCase
{
    use RefreshDatabase;

    private function sentQuotation(): Quotation
    {
        $admin = $this->staff(Role::Admin);
        $customer = Customer::factory()->create(['company_id' => $admin->company_id]);
        Contact::factory()->for($customer)->primary()->create(['email' => 'buyer@acme.se']);

        return Quotation::factory()->sent()->create([
            'company_id' => $admin->company_id,
            'customer_id' => $customer->id,
        ]);
    }

    public function test_accepting_thanks_the_customer_and_alerts_the_team(): void
    {
        Mail::fake();
        $quotation = $this->sentQuotation();

        $this->post(route('public.quotations.accept', $quotation->public_token), [
            'decision_note' => 'Looks great.',
        ]);

        Mail::assertSent(QuotationAcceptedMail::class, fn ($mail) => $mail->hasTo('buyer@acme.se'));
        Mail::assertSent(AdminAlertMail::class);
    }

    public function test_declining_alerts_the_team_without_thanking(): void
    {
        Mail::fake();
        $quotation = $this->sentQuotation();

        $this->post(route('public.quotations.reject', $quotation->public_token), [
            'decision_note' => 'Too expensive right now.',
        ]);

        Mail::assertNotSent(QuotationAcceptedMail::class);
        Mail::assertSent(AdminAlertMail::class);
    }

    public function test_customer_can_comment_on_a_quotation_and_the_team_is_notified(): void
    {
        Mail::fake();
        $quotation = $this->sentQuotation();

        $this->post(route('public.quotations.comment', $quotation->public_token), [
            'author_name' => 'Anna',
            'body' => 'Can we start in January?',
        ]);

        $this->assertDatabaseHas('comments', [
            'commentable_type' => Quotation::class,
            'commentable_id' => $quotation->id,
            'body' => 'Can we start in January?',
            'is_internal' => false,
        ]);

        Mail::assertSent(AdminAlertMail::class);
    }

    public function test_customer_can_follow_a_project_without_logging_in(): void
    {
        $admin = $this->staff(Role::Admin);
        $project = Project::factory()->for($admin, 'owner')->create(['name' => 'Website relaunch']);

        $this->get(route('public.projects.show', $project->public_token))
            ->assertOk()
            ->assertSee('Website relaunch');
    }

    public function test_a_project_hidden_from_the_customer_is_not_reachable(): void
    {
        $admin = $this->staff(Role::Admin);
        $project = Project::factory()->for($admin, 'owner')->create(['customer_can_view' => false]);

        $this->get(route('public.projects.show', $project->public_token))->assertNotFound();
    }

    public function test_customer_can_message_on_a_project_and_the_team_is_notified(): void
    {
        Mail::fake();
        $admin = $this->staff(Role::Admin);
        $customer = Customer::factory()->create(['company_id' => $admin->company_id]);
        Contact::factory()->for($customer)->primary()->create(['name' => 'Erik Lund']);
        $project = Project::factory()->for($admin, 'owner')->create(['customer_id' => $customer->id]);

        // The link identifies the customer, so the form never asks who they are.
        $this->post(route('public.projects.comment', $project->public_token), [
            'body' => 'Any update on the launch date?',
        ]);

        $comment = $project->comments()->firstOrFail();

        $this->assertSame('Any update on the launch date?', $comment->body);
        $this->assertSame('Erik Lund', $comment->authorName());
        $this->assertTrue($comment->fromCustomer());

        Mail::assertSent(AdminAlertMail::class);
    }

    public function test_internal_notes_are_hidden_from_the_customer(): void
    {
        $admin = $this->staff(Role::Admin);
        $project = Project::factory()->for($admin, 'owner')->create();

        $project->addStaffComment('Internal: margin is thin on this one.', $admin->id, internal: true);
        $project->addStaffComment('We start on Monday.', $admin->id);

        $response = $this->get(route('public.projects.show', $project->public_token));

        $response->assertOk()
            ->assertSee('We start on Monday.')
            ->assertDontSee('margin is thin');
    }

    public function test_a_comment_knows_whether_it_came_from_the_customer(): void
    {
        $admin = $this->staff(Role::Admin);
        $project = Project::factory()->for($admin, 'owner')->create();

        $staff = $project->addStaffComment('From us.', $admin->id);
        $customer = $project->addCustomerComment('From them.', null, 'Erik');

        $this->assertFalse($staff->fromCustomer());
        $this->assertTrue($customer->fromCustomer());
        $this->assertSame('Erik', $customer->authorName());
    }

    public function test_comment_body_is_required(): void
    {
        $admin = $this->staff(Role::Admin);
        $project = Project::factory()->for($admin, 'owner')->create();

        $this->post(route('public.projects.comment', $project->public_token), ['body' => ''])
            ->assertSessionHasErrors('body');

        $this->assertSame(0, Comment::count());
    }
}
