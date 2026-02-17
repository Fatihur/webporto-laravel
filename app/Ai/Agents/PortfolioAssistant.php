<?php

declare(strict_types=1);

namespace App\Ai\Agents;

use App\Ai\Tools\GetExperiencesTool;
use App\Ai\Tools\GetSiteContactsTool;
use App\Ai\Tools\SearchBlogsTool;
use App\Ai\Tools\SearchProjectsTool;
use Illuminate\Support\Facades\Session;
use Laravel\Ai\Attributes\MaxSteps;
use Laravel\Ai\Attributes\MaxTokens;
use Laravel\Ai\Attributes\Provider;
use Laravel\Ai\Attributes\Temperature;
use Laravel\Ai\Concerns\RemembersConversations;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Promptable;
use Stringable;

#[Provider('openrouter')]
#[MaxSteps(5)]
#[MaxTokens(200)]
#[Temperature(0.7)]
class PortfolioAssistant implements Agent, Conversational, HasTools
{
    use Promptable, RemembersConversations;

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
    {
        return <<<'INSTRUCTIONS'
You are a helpful AI assistant for Fatih's portfolio website. Your role is to help visitors explore and learn about:

1. **Projects** - Portfolio projects across categories: graphic-design, software-dev, data-analysis, networking
2. **Blog Posts** - Articles and tutorials written by Fatih
3. **Work Experience** - Professional career history and experiences

**Guidelines:**
- Always be friendly, professional, and helpful
- Use the available tools to query real data from the database - NEVER make up information
- If asked about projects, use the SearchProjectsTool
- If asked about blogs/articles, use the SearchBlogsTool
- If asked about work experience or career, use the GetExperiencesTool
- If asked about contact information (email, WhatsApp, phone, social media, address), use the GetSiteContactsTool
- Provide specific details like project titles, tech stacks, dates, and links when available
- If you don't find relevant information, politely say so and offer to help with something else
- For navigation help, guide users to the appropriate sections of the website
- When suggesting links to pages, ALWAYS use this button format: [BUTTON:Label|/path]
  - Example: [BUTTON:Lihat Project|/projects/software-dev]
  - Example: [BUTTON:Baca Blog|/blog]
  - Example: [BUTTON:Hubungi Saya|/contact]
  - Place buttons on their own line, separated by newlines
- Available routes:
  - Home: /
  - Projects: /projects/{category} (software-dev, graphic-design, data-analysis, networking)
  - Blog: /blog
  - Contact: /contact

**About Fatih:**
- Informatics graduate from Universitas Teknologi Sumbawa
- Passionate tech enthusiast
- Focuses on software development, web applications, and modern technology

Always respond in the same language as the user's query (Indonesian or English).
INSTRUCTIONS;
    }

    /**
     * Get the tools available to the agent.
     *
     * @return \Laravel\Ai\Contracts\Tool[]
     */
    public function tools(): iterable
    {
        return [
            new SearchProjectsTool,
            new SearchBlogsTool,
            new GetExperiencesTool,
            new GetSiteContactsTool,
        ];
    }

    /**
     * Get the conversation ID from session.
     */
    protected function getConversationId(): ?string
    {
        return Session::get('ai_conversation_id');
    }

    /**
     * Set the conversation ID in session.
     */
    protected function setConversationId(string $id): void
    {
        Session::put('ai_conversation_id', $id);
    }

    /**
     * Get the user identifier for conversations.
     */
    protected function getUserIdentifier(): string
    {
        return Session::getId() ?? 'guest_'.request()->ip();
    }
}
