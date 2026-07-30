# LexiFlow AI — Architecture Decision Records (ADR)
### The Canonical Decision Register — Master Document

**Document Class:** Architecture Decision Records (Decision Log)
**Tier:** Level 3 — Implementation-enabling (formalizes decisions the SAD/SDD left open or implied)
**Owner:** Chief Software Architect, LexiFlow AI
**Date of Record:** 2026-07-29
**Review Model:** Supersession-only — ADRs are never deleted; a superseded ADR is marked and links forward.

---

## Document Control

| Attribute | Value |
|---|---|
| Document type | ADR Master Collection (ADR-001 → ADR-060) |
| Predecessors | PRD, CEO Vision, Product Strategy, CTO Constitution, Domain Model, SAD, SDD |
| Authority of predecessors | **Immutable.** This document formalizes engineering decisions; it never rewrites, summarizes, replaces, or reinterprets business/architecture/domain decisions already fixed in Levels 0–2. |
| Authority of this document | The single source of truth for *technical* decisions. Every future engineer, AI coding assistant, architect, and CTO follows these ADRs unless an ADR is formally superseded. |
| Conflict protocol | Per CTO Constitution §12 — explain, ADR, proceed on existing decision. Never silently resolve. |
| Code/UI/API/Schema generation | **None.** This is a decision document. No implementation code, APIs, DB schema, UI, or sprint plans. |

---

## 0. How to Use This Document

### 0.1 Purpose of the ADR Collection

An Architecture Decision Record captures *one* significant technical decision: the context that demanded it, the choice made, the alternatives rejected and why, and the consequences accepted. This collection is the **decisional spine** of LexiFlow AI. It exists for three audiences:

1. **Future engineers and architects** joining in year three, who must understand *why* the system is shaped as it is — not merely *what* it is.
2. **AI coding assistants** (CTO Constitution §45) with no session memory, who must follow a decision rather than reinvent one inside an unrelated task.
3. **Future CTOs and reviewers**, who must be able to audit the reasoning trail behind any load-bearing choice rather than inherit it as folklore.

Every ADR answers **WHY**, not only WHAT. Every decision states why alternatives lost, what technical debt it introduces, and what future migration path exists.

### 0.2 Relationship to the Immutable Documents

The seven predecessor documents are **complete and immutable**. This ADR collection does not reopen, summarize, or reinterpret them. Its role is narrower and precise: to **formalize as numbered, reviewable decisions** the engineering choices that the Constitution, Domain Model, SAD, and SDD either (a) explicitly deferred to "a later ADR," (b) stated as provisional defaults, or (c) implied as implementation-level commitments. Where an ADR appears to touch a decision the predecessors made, it is *recording and ratifying* that decision in decision-log form — not changing it.

### 0.3 Numbering Reconciliation — CONFLICT RESOLUTION (Binding)

**The conflict.** The prior documents established a *provisional* ADR scheme that evolved as the document series grew:

- **Domain Model §23** recommended 5 ADRs (unnumbered except by topic).
- **SAD §88** listed 9 candidate ADRs.
- **SDD §59** consolidated these into a 15-ADR register with specific titles (e.g., *ADR-001 = Learner Model/Scheduling boundary; ADR-006 = PostgreSQL; ADR-015 = cache-hit-rate threshold*).

The task now mandates a **canonical, comprehensive 60-ADR scheme** with different titles bound to different numbers (e.g., *ADR-001 = Core Domain Boundary; ADR-002 = Learner Model vs Scheduling Context; ADR-021 = Database Selection*).

These two schemes assign **different meanings to the same numbers** (legacy "ADR-001" ≠ master "ADR-001"). Silently adopting the new scheme would orphan every ADR cross-reference embedded in the immutable Domain Model, SAD, and SDD. Per CTO Constitution §12 and the governing instruction of this task, this conflict is **not silently resolved**. It is resolved by the following binding rule, recorded here as the conflict-resolution record:

> **Resolution (binding).** The 60-ADR scheme defined in this Master Document is the **canonical register** henceforth. The provisional numbers used in the Domain Model, SAD, and SDD are **legacy references**, valid only against the cross-reference table below. Every legacy ADR decision is preserved **verbatim in substance** under its new master number; nothing is lost, merged away, or reinterpreted. All future citations use master numbers. Where a predecessor document cites a legacy number, readers resolve it through this table.

**Legacy → Master cross-reference table:**

| Legacy reference (Domain Model §23 / SAD §88 / SDD §59) | Legacy topic | Master ADR (this document) | Treatment |
|---|---|---|---|
| Legacy ADR-001 | Learner Model / Scheduling boundary | **ADR-002** | 1:1, renumbered |
| Legacy ADR-002 | Pronunciation scope & provider | **ADR-016 + ADR-017** | Split into provider (016) and scope (017) |
| Legacy ADR-003 | "AI Tutor" non-service guardrail | **ADR-005 (Module Organization)** + **ADR-004 (DDD Strategy)** | Folded; both ADRs explicitly ratify the guardrail |
| Legacy ADR-004 | Content-eligibility policy enforcement | **ADR-004 (DDD Strategy)** + **ADR-023 (Storage Strategy)** | Folded; enforcement mechanism recorded in both |
| Legacy ADR-005 | Second-L1 parametrization | **ADR-059 (Future Language Expansion)** | 1:1, renumbered |
| Legacy ADR-006 | PostgreSQL | **ADR-021 (Database Selection)** | 1:1, renumbered |
| Legacy ADR-007 | FSRS algorithm | **ADR-002 (Learner Model vs Scheduling)** | Folded; FSRS recorded as the Scheduling-context algorithm decision |
| Legacy ADR-008 | Secrets manager + S3 | **ADR-040 (Secrets)** + **ADR-023 (Storage)** | Split |
| Legacy ADR-009 | Feature-flag service | **ADR-038 (Feature Flags)** | 1:1, renumbered |
| Legacy ADR-010 | Terraform + ECS/EKS | **ADR-032 (Deployment)** | 1:1, renumbered |
| Legacy ADR-011 | Data Mapper pattern | **ADR-010 (Repository Pattern)** | Folded; data-mapper is the repository-implementation decision |
| Legacy ADR-012 | File-naming convention mapping | **ADR-047 (Coding Standards)** | Folded |
| Legacy ADR-013 | LLM provider + fallback | **ADR-014 (LLM Provider Strategy)** | 1:1, renumbered |
| Legacy ADR-014 | Message broker | **ADR-012 (Event Bus)** + **ADR-020 (Queue Architecture)** | Split |
| Legacy ADR-015 | Cache-hit-rate threshold | **ADR-058 (Cost Monitoring)** | Folded |

**No decision was lost or altered in this reconciliation.** This table is the authoritative bridge between the immutable predecessors and this canonical register. It is itself the ADR-pattern conflict resolution the Constitution requires.

### 0.4 ADR Status Vocabulary

| Status | Meaning |
|---|---|
| **Accepted** | The decision is in force; implementation proceeds on it. |
| **Accepted (Conditional)** | Accepted, but explicitly contingent on a named future signal (e.g., production data, GTM plan). Reversal path documented. |
| **Proposed** | Recommended but awaiting human approval; not yet safe to implement against as final. |
| **Superseded** | Replaced by a later ADR; linked forward; reasoning preserved. |
| **Deprecated** | No longer in force; retained for history. |

### 0.5 ADR Template (used by every record below)

Every ADR contains, without omission: **ADR Number · Title · Status · Date · Context · Problem Statement · Decision · Alternatives Considered · Pros · Cons · Trade-offs · Consequences · Risks · Migration Strategy · Related Documents · Future Revisions.**

---

## Table of Contents

**Foundational Architecture (ADR-001 → ADR-012)**
1. Core Domain Boundary
2. Learner Model vs Scheduling Context
3. Hexagonal Architecture
4. DDD Strategy
5. Module Organization
6. Laravel Modular Monolith
7. CQRS Usage
8. Event Driven Architecture
9. Transactional Outbox
10. Repository Pattern
11. Domain Events
12. Event Bus

**AI & Speech (ADR-013 → ADR-017)**
13. AI Gateway
14. LLM Provider Strategy
15. Prompt Versioning
16. Speech Recognition Provider
17. Pronunciation MVP Scope

**Data, Cache & Infrastructure (ADR-018 → ADR-023)**
18. Cache Strategy
19. Shared Explanation Cache
20. Queue Architecture
21. Database Selection
22. Search Engine
23. Storage Strategy

**Security & Identity (ADR-024 → ADR-026)**
24. Authentication
25. Authorization
26. RBAC

**API & Contracts (ADR-027 → ADR-029)**
27. API Versioning
28. REST vs GraphQL
29. OpenAPI First

**Delivery, Quality & Observability (ADR-030 → ADR-036)**
30. Testing Pyramid
31. CI/CD
32. Deployment Strategy
33. Observability
34. Logging
35. Metrics
36. Monitoring

**Reliability, Cost & Control (ADR-037 → ADR-042)**
37. Error Handling
38. Feature Flags
39. Configuration Management
40. Secrets Management
41. Privacy
42. Data Retention

**Resilience & Scale (ADR-043 → ADR-046)**
43. Backup Strategy
44. Disaster Recovery
45. Scaling Strategy
46. Microservice Migration Strategy

**Engineering Culture & Governance (ADR-047 → ADR-056)**
47. Coding Standards
48. AI Coding Rules
49. Dependency Policy
50. Technical Debt Policy
51. Versioning Strategy
52. Migration Strategy
53. Architecture Governance
54. Architecture Review Process
55. Engineering Review Process
56. Security Review Process

**Non-Functional & Evolution (ADR-057 → ADR-060)**
57. Performance Budgets
58. Cost Monitoring
59. Future Language Expansion
60. Architecture Evolution Strategy

---

# Part I — Foundational Architecture

## ADR-001: Core Domain Boundary

| Field | Value |
|---|---|
| **Status** | Accepted |
| **Date** | 2026-07-29 |
| **Supersedes** | — |
| **Related** | Domain Model §2, §5; SAD §1, §4; ADR-002, ADR-004, ADR-005 |

### Context
The Domain Model (§2) defines the **Core Domain** of LexiFlow as *Learner Modeling & Adaptive Scheduling* — the persistent, evolving representation of what a specific learner knows, how confidently they know it, and when they will next need to be reminded of it, plus the logic deciding what to surface next. Every other capability (transcription, translation, quiz generation) is explicitly modeled as *subordinate infrastructure* serving this core (Domain Model §3). The SAD (§4) ranks "Correctness of the Learner Model" as the #1 quality attribute — a silently wrong Mastery update is worse than a slow one. Product Strategy (§10) names the persistent Learner Model as the company's most durable moat.

### Problem Statement
The system contains many modules. Engineering effort, test rigor, review severity, and architectural protection are finite. Without an explicit, immutable boundary declaring which parts are the *competitive core* versus *subordinate/generic*, three failure modes emerge: (1) the core receives the same engineering treatment as commodity plumbing, letting regressions into the company's actual IP; (2) core logic gets coupled to adapters/infrastructure, destroying the swap-and-extract seams; (3) "the core" drifts as engineers quietly redefine it. We must fix the boundary authoritatively and make it enforceable.

### Decision
**Adopt the Core Domain boundary exactly as defined in Domain Model §2 and enforce it as the system's highest-protection zone.**

- **Core Domain** = the `LearnerModel` and `Scheduling` contexts (the Learner Model aggregate, Mastery state, the spaced-repetition interval logic). This is the moat.
- **Supporting Domains** = Content Import, Linguistic Analysis, Pronunciation, Curriculum Alignment, Classroom, Engagement (necessary, non-trivial, but not the differentiation).
- **Generic Domains** = Identity, Billing, Delivery, Storage (boring, off-the-shelf, Conformist).
- The Core Domain receives: a framework-free pure Domain layer (ADR-003, ADR-010), the deepest test investment including property-based tests (ADR-030), one-aggregate-per-transaction discipline (SAD §76), elevated review (ADR-054/056), and is the **last** candidate for service extraction (ADR-046).
- Machine-checkable enforcement: the Core Domain's `Domain` namespace must have zero `Illuminate\*` imports (architecture tests); its coverage bar is the highest in the suite; changes to it trigger architecture review (ADR-054).

### Alternatives Considered
1. **Treat all modules as equal peers.** Rejected — contradicts Domain Model §2 and Product Strategy §10; would let the moat erode under commodity-grade engineering.
2. **Make Linguistic Analysis co-equal core** (translation quality is also a differentiator). Rejected as a *structural* change — translation quality is critical (and gets its own rigor, ADR-015), but the Domain Model is explicit that only Learner Modeling & Adaptive Scheduling is *not externally replicable*; translation, while hard, is replicable with effort. Honoring the immutable boundary.
3. **Leave the boundary implicit.** Rejected — invites drift; CTO Constitution §45 warns AI assistants will quietly reshape implicit boundaries.

### Pros
- Focuses the scarcest engineering rigor where it compounds into competitive advantage.
- Makes the moat's protection enforceable in CI, not just in review.
- Aligns extraction sequencing (ADR-046) to protect the core last.

### Cons
- Creates a two-tier engineering standard that requires explicit communication so supporting/generic modules aren't neglected.
- Risk of over-protecting the core and under-investing in the Bangla translation quality that the CEO Vision (§7) calls trust-critical — mitigated by giving Linguistic Analysis its own quality rigor (ADR-015).

### Trade-offs
We accept a deliberately *unequal* distribution of engineering rigor across modules, because the business value is deliberately unequal (Domain Model §2). Equality of engineering treatment is not a virtue when the value at stake is not equal.

### Consequences
- Core Domain code is held to a higher bar (purity, testing, review) than the rest — by design.
- Any proposal to add logic to the Core Domain that could live elsewhere is pushed outward.
- The Core Domain is extraction-reserved (ADR-046): it stays in-process longest.

### Risks
- **Boundary erosion under deadline pressure** — mitigated by architecture tests + ADR-054 review gate.
- **Misclassifying a future capability as "core"** to get more rigor — mitigated by requiring an ADR to reclassify.

### Migration Strategy
None required — this is a greenfield boundary decision. Future reclassification requires superseding this ADR.

### Related Documents
Domain Model §2–§5; SAD §1, §4, §53; Product Strategy §10; CTO Constitution §38; SDD §1 (P1), §43.

### Future Revisions
- If production data shows a Supporting Domain (e.g., Pronunciation) accreting core-like strategic value, a reclassification ADR is required — not a quiet promotion.

---

## ADR-002: Learner Model vs Scheduling Context

| Field | Value |
|---|---|
| **Status** | Accepted (Conditional) |
| **Date** | 2026-07-29 |
| **Supersedes** | Domain Model §23 ADR #1 (legacy ADR-001); SAD §15 provisional default; SDD §59 (FSRS folded in) |
| **Related** | ADR-001, ADR-007, ADR-009, ADR-011, ADR-046 |

### Context
This is the single most consequential open modeling question carried across the entire document series. The Domain Model (§6) modeled Learner Model and Scheduling as two Bounded Contexts with a *Shared Kernel candidate* relationship, explicitly flagging (§22) that they may instead be one context with two aggregates — and that this decision "materially affects team ownership structure, not just code organization." The Domain Model (§23) and SAD (§88) deferred resolution to an ADR. The SAD (§15) adopted a *provisional default* of "one module, two aggregates, sharing a transaction boundary," and the SDD (§14) implemented against it. This ADR now formalizes the decision. It also records the **spaced-repetition algorithm choice (FSRS v5)** as the Scheduling-context's defining algorithm decision (folded from legacy ADR-007).

### Problem Statement
A Review Session completion must, atomically: (a) finalize the session, (b) compute each item's next-review interval from its Mastery history, (c) update Mastery, and (d) publish `ReviewSessionCompleted` and, on threshold crossing, `MasteryThresholdReached`. If Learner Model and Scheduling are separate *services*, this tight loop crosses a network/transaction boundary — risking consistency bugs (a Mastery update lost or double-counted) in the exact data the company's moat depends on (Domain Model §16 invariant). If they are *one context*, the loop is an in-process transaction but the two contexts' conceptual distinctness is blurred and future independent evolution is harder. We must choose, and we must choose the SRS algorithm that this loop runs.

### Decision
**Adopt the combined-module default: Learner Model and Scheduling are ONE deployable module containing TWO aggregates (`LearnerModel` aggregate, `ReviewSession` aggregate), sharing a database connection and transaction boundary, while remaining separately namespaced (`App\LearnerModel`, `App\Scheduling`) and publishing Domain Events for all external consumers.**

Concretely:
- The tight loop (`ReviewSession.complete()` → Interval Calculator → `LearnerModel.applyInteractionOutcome()` → next-due-time) executes **in-process within a single transaction** (SAD §76, one-aggregate-modified-per-transaction is preserved because the Mastery update is applied *through* the LearnerModel aggregate's own method, not by the ReviewSession reaching into it).
- External consumers (Engagement, Curriculum Alignment) receive `ReviewSessionCompleted` and `MasteryThresholdReached` via the Event Bus exactly as if the contexts were split — so the *external* shape is already split-ready.
- **SRS algorithm: FSRS v5 (Free Spaced Repetition Scheduler)** as the Interval Calculator, with SM-2 retained as a documented fallback. FSRS is selected for: empirical strength, open tunability per learner (supporting the personalization promise), and a stability/difficulty model that maps cleanly onto Mastery Value Objects (SDD §10). MVP ships FSRS global defaults; per-learner parameter optimization is deferred to V2 (Product Strategy §22).

### Alternatives Considered
1. **Strict Customer/Supplier split (two services).** Rejected at MVP — deploying a networked version of a still-uncertain boundary is "strictly worse than deploying an in-process version of the same boundary and correcting it cheaply if wrong" (SAD §8). The consistency risk to Mastery data is unacceptable at MVP scale.
2. **Full Shared Kernel (single context, single aggregate).** Rejected — collapses the conceptual distinction the Domain Model preserved; loses the clean seam for future split.
3. **SM-2 as the SRS algorithm.** Rejected as primary — older, less empirically strong, less tunable per learner; retained as fallback only.
4. **Fixed-interval scheduling.** Rejected — defeats the learning-science commitment (PRD §20, CEO Vision §9).

### Pros
- The Mastery-update ↔ next-review-time loop is consistent and fast (in-process transaction) — protecting the #1 quality attribute (ADR-001).
- External interactions are already event-driven, so a future split (if production evidence demands it) is a contained, low-cost migration.
- FSRS gives research-grade retention modeling with a per-learner optimization path.
- Two separately-namespaced aggregates preserve conceptual clarity and team-ownership optionality.

### Cons
- Internal coupling between two aggregates is an *acknowledged bet* (SAD §84) — if the boundary later proves it should be split, there is rework (contained to the in-process path).
- FSRS introduces parameters the team must understand and eventually tune.

### Trade-offs
We trade a small, contained future-split risk for **immediate correctness and performance on the company's most valuable data**. This is the correct trade at MVP scale: correctness of the moat outweighs speculative distributed-systems benefits.

### Consequences
- `LearnerModel` and `Scheduling` deploy together; both are Core Domain (ADR-001) and extraction-reserved (ADR-046).
- FSRS parameters live in versioned config (ADR-039); changes to them are ADR-worthy (they alter pedagogical behavior — Domain Model §15).
- Mastery updates are only ever possible through the sanctioned interaction-application path (Domain Model §14) — this combined-module transaction is the *only* place that path's atomicity is guaranteed.

### Risks
- **Conditional risk:** if real production load/team ownership later shows the combined module hinders independent deploy cadence (SAD §53, 100k+ stage), a split is needed — mitigated by event-ready external shape.
- **FSRS tuning risk:** poor parameter choices degrade retention silently — mitigated by starting with validated global defaults and deferring per-learner optimization until enough interaction history exists.

### Migration Strategy
If a future split is required: convert the in-process Mastery-update call to a cross-module Application-layer call (already the right shape) or an event; external consumers are untouched. The migration is contained because external-facing interactions already use events. Cost: low-to-moderate.

### Related Documents
Domain Model §6, §11, §15, §17, §22, §23; SAD §15, §49, §76, §84, §88; SDD §10, §14, §42, §43; PRD §20.

### Future Revisions
- Revisit at the SAD §53 100k-user stage: does the combined module still serve, or does deploy-cadence friction justify a split?
- V2: enable per-learner FSRS optimization once interaction history is sufficient.

---

## ADR-003: Hexagonal Architecture

| Field | Value |
|---|---|
| **Status** | Accepted |
| **Date** | 2026-07-29 |
| **Related** | ADR-001, ADR-004, ADR-006, ADR-010, ADR-013 |

### Context
The SAD (§8–§9) mandates that each module be internally structured with **Clean/Hexagonal Architecture** — a Domain core, an Application/use-case layer, and Infrastructure adapters — with dependencies pointing strictly inward. The CTO Constitution (§2, §5) makes "dependencies point inward toward the domain" a load-bearing principle and ties it directly to the content-agnostic core / adapter boundary that lets the company add a content type in a sprint and an L1 language in a quarter (SAD §1).

### Problem Statement
Without an enforced inward-dependency topology, three classes of defect accumulate: (1) provider/framework specifics leak into the Domain, making a provider or DB swap a system-wide refactor; (2) the Core Domain becomes untestable in isolation; (3) the extraction seam (ADR-046) is destroyed because Infrastructure concerns are welded into business logic. We need an architectural pattern that makes "dependencies point inward" structural and verifiable, not aspirational.

### Decision
**Adopt Hexagonal Architecture (Ports & Adapters) as the internal pattern for every module, with the Clean Architecture dependency rule: Domain → Application → Infrastructure, where Infrastructure *implements* Domain-defined ports.**

- **Domain layer:** pure PHP, zero `Illuminate\*`, zero provider SDKs — the only place Core Domain logic lives (ADR-001). Defines repository *ports* (interfaces).
- **Application layer:** orchestrates use cases, depends only on Domain ports, owns the transaction boundary.
- **Infrastructure layer:** the sole layer permitted to import Laravel, Eloquent, Redis, AWS, provider SDKs; *implements* Domain ports.
- The AI Gateway (ADR-013) and Speech Gateway (ADR-016) are the architectural embodiment of this pattern for external AI/ASR providers — no Domain/Application code may call a provider SDK directly.
- **Enforcement is machine-checkable:** architecture tests (Pest) + PHPStan assert that `App\*\Domain\*` has no outward dependencies; CI fails on violation (SDD §5).

### Alternatives Considered
1. **Conventional Laravel (Active Record Models + Controllers + Services).** Rejected — couples Domain to Eloquent, makes the Core Domain impure and untestable in isolation, destroys provider-swap and extraction seams. This is the dominant reason Laravel projects fail at DDD.
2. **Layered (n-tier) without the ports inversion.** Rejected — Infrastructure would still be a dependency of Domain, not an implementer of Domain ports; the inversion is the whole point.
3. **Pure Clean Architecture without explicit ports.** Rejected — ports make the contract explicit and swappable; implicit boundaries drift under AI-assisted development (CTO Constitution §45).

### Pros
- Domain is testable with zero infrastructure (fast, deterministic tests for the moat).
- Provider, database, and framework swaps are contained to Infrastructure adapters.
- The pattern is uniform across modules (SAD §14), so "how a module works" is learned once.
- The extraction seam (ADR-046) is structural: a module's Infrastructure adapter becomes a network adapter at extraction, Domain/Application unchanged.

### Cons
- More classes per feature than stock Laravel; higher ceremony.
- Requires discipline to keep Application thin (orchestration only) — a constant review pressure.
- Eloquent's ergonomics are unavailable in the Domain (resolved by the Data Mapper in ADR-010).

### Trade-offs
We accept higher per-feature ceremony and explicit mapping code in exchange for **purity, testability, and swap/extract seams** — the three properties that determine whether the system survives a provider change, a database change, or a scale-driven extraction without a rewrite.

### Consequences
- Every module follows the identical layer shape (SDD §4); the scaffold enforces it.
- "No provider SDK in Domain/Application" is a hard, CI-enforced rule (ADR-013).
- The Repository pattern (ADR-010) is the primary port/adapter mechanism for persistence.

### Risks
- **Discipline erosion** — engineers under deadline pressure reach for Eloquent in the Domain. Mitigated by architecture tests (CI-blocking) and ADR-054 review.

### Migration Strategy
Greenfield; no migration. Adopted from first commit.

### Related Documents
SAD §8, §9, §25–§28; CTO Constitution §2, §5; SDD §2, §5; Domain Model §19.

### Future Revisions
- If a module proves its Domain layer is trivially thin and the ceremony isn't earning its keep, an ADR may relax the pattern *for that module only* — never for the Core Domain.

---

## ADR-004: DDD Strategy

| Field | Value |
|---|---|
| **Status** | Accepted |
| **Date** | 2026-07-29 |
| **Supersedes** | Legacy ADR-003 (AI Tutor guardrail) and legacy ADR-004 (content-eligibility enforcement) — both folded in here |
| **Related** | ADR-001, ADR-005, ADR-010, ADR-011, ADR-023 |

### Context
The CTO Constitution (§2) adopts Domain-Driven Design with a specific, non-negotiable emphasis: the Ubiquitous Language of the codebase must match the language of the PRD, CEO Vision, Product Strategy, and Domain Model *exactly* (learner, content source, review session, mastery — not generic "item"/"record"). The Domain Model (§5–§21) provides the full DDD specification: Bounded Contexts, aggregates, entities, value objects, domain events, domain services, invariants, and the Context Map. This ADR ratifies the DDD strategy and records two folded decisions: the **"AI Tutor" non-service guardrail** (legacy ADR-003) and the **content-eligibility policy enforcement mechanism** (legacy ADR-004).

### Problem Statement
DDD is frequently adopted superficially (folders named "Domain" with anemic models) or abandoned when it conflicts with framework ergonomics. We must commit to DDD *as actually practiced* — with real aggregates that enforce invariants, a Ubiquitous Language that is policed, and bounded contexts that are respected as integration contracts. Separately, two specific risks need structural decisions: (a) the term "AI Tutor" (Domain Model §7) is a cross-cutting experiential concept with no owning context — well-meaning engineers will be tempted to build a literal `AITutorService` that violates every boundary; (b) the content-eligibility policy (Domain Model §15, sourced from the PRD's copyright analysis) originates in an immutable legal-risk document and must be enforced as code at the Content Import boundary.

### Decision
**Adopt DDD as the primary design discipline, implemented exactly as the Domain Model specifies, with these binding sub-decisions:**

1. **Aggregates enforce invariants internally.** No anemic models; no public setters that bypass invariants (e.g., Mastery is updateable only via the sanctioned interaction path — Domain Model §14). One aggregate is modified per transaction (SAD §76).
2. **Ubiquitous Language is policed.** Class/identifier names must use Domain Model §7 terms verbatim; CI linting flags renamed domain terms (e.g., `User` instead of `Learner`).
3. **Bounded Contexts communicate only via published Application interfaces or Domain Events** (Domain Model §6; ADR-008/ADR-011) — never by reaching into another context's Domain or tables (ADR-005).
4. **"AI Tutor" guardrail (legacy ADR-003, ratified):** "AI Tutor" is a cross-cutting experiential term, **not a service, module, package, or aggregate.** No `AITutor*` class, namespace, or service is created. The emergent tutor behavior is produced by `LearnerModel` + `Scheduling` + `LinguisticAnalysis` cooperating; engineering reasons in terms of those owning contexts. This guardrail exists specifically to prevent the risk flagged in Domain Model §22.
5. **Content-eligibility enforcement (legacy ADR-004, ratified):** the eligibility policy (format/size/source-type restrictions from the PRD copyright analysis) is enforced **as the very first transition of the ContentSource aggregate's state machine**, before any processing cost is incurred — implemented as a Domain policy object invoked by the aggregate, with rules sourced from the PRD. Changing the rules to loosen restrictions requires checking back against the immutable PRD, not an engineering decision alone.

### Alternatives Considered
1. **Anemic models + service-oriented business logic.** Rejected — scatters invariants, makes the "honesty over flattery" Mastery rule unenforceable, and produces the silent-integrity bugs DDD exists to prevent.
2. **A literal `AITutorService` orchestrating everything.** Rejected (legacy ADR-003) — violates every Bounded Context boundary; the Domain Model is explicit this must never exist.
3. **Content-eligibility as an Infrastructure/gateway check only.** Rejected — it's a *domain* rule sourced from a *business* document; it belongs in the ContentSource aggregate's domain logic, invoked before cost is spent.
4. **Relaxed Ubiquitous Language ("close enough" naming).** Rejected — divergent understanding of shared terms is among the most expensive, hardest-to-detect failure modes in a growing org (Domain Model §21).

### Pros
- Invariants (Mastery honesty, absolute isolation, on-request translation, transcript immutability) become structurally unbreakable rather than policy-discouraged.
- The "AI Tutor" anti-pattern is prevented by an explicit, citable guardrail.
- Copyright-risk control is enforced in domain logic before cost — both a legal and a cost control.
- Engineers, product, and AI assistants reason about identical concepts.

### Cons
- DDD done properly is more verbose than CRUD-style Laravel.
- Requires sustained discipline to keep aggregates cohesive and contexts from leaking.

### Trade-offs
We accept verbosity and discipline cost in exchange for **structural enforcement of the business's most important invariants** — the same invariants the CEO Vision and Product Strategy elevate to "permanent kill criteria" (Product Strategy §48).

### Consequences
- The Domain layer is the authoritative home for business rules; Presentation/Infrastructure hold none.
- The "AI Tutor" guardrail is citable in review (ADR-054) to reject any `AITutorService` proposal.
- Eligibility policy changes trace back to the PRD, preserving the legal-risk reasoning chain.

### Risks
- **Aggregate boundary mis-drawing** — too-large aggregates cause contention, too-small cause consistency gaps. Mitigated by the one-aggregate-per-transaction rule and ADR-054 review.
- **"AI Tutor" reification attempts** under product pressure — mitigated by this explicit guardrail.

### Migration Strategy
Greenfield. Future aggregate-boundary changes follow the Domain Model §21 evolution strategy (new context or ADR, never quiet stretching).

### Related Documents
Domain Model §5–§21, §22, §23; CTO Constitution §2, §45; SAD §25; SDD §1, §4, §14; PRD §36 (copyright).

### Future Revisions
- ADR-059 addresses whether Linguistic Analysis/Curriculum become L1-parametrized for the second language (Domain Model §20 open question).

---

## ADR-005: Module Organization

| Field | Value |
|---|---|
| **Status** | Accepted |
| **Date** | 2026-07-29 |
| **Related** | ADR-001, ADR-003, ADR-004, ADR-006, ADR-012 |

### Context
The CTO Constitution (§7) mandates top-level organization **by domain concept, not by technical layer** — `content-import/`, `learner-model/`, `spaced-repetition/` as the first level, never `controllers/`, `services/`, `models/`. The SAD (§16) maps the Domain Model's Bounded Contexts to modules; the SDD (§3, §6) fixes the physical layout and the 14-module package structure. This ADR ratifies the module organization and binds it to the "AI Tutor non-service" guardrail (legacy ADR-003, also recorded in ADR-004).

### Problem Statement
A modular monolith's value depends entirely on the modules being **real boundaries**, not just folders. If modules share tables, import each other's internals, or have no single-sentence responsibility, the "modular" prefix is a lie and extraction (ADR-046) becomes a rewrite. We must fix module granularity, ownership, public interfaces, and the forbidden cross-module operations — enforceably.

### Decision
**Adopt the 14-module package structure from SDD §6, organized domain-first at the namespace root, with these binding rules:**

- **Modules** = the Bounded Contexts: `LearnerModel`, `Scheduling` (combined per ADR-002 but separately namespaced), `ContentImport`, `LinguisticAnalysis`, `Pronunciation`, `CurriculumAlignment`, `Classroom`, `Engagement`, `Identity`, `Billing`, `Delivery`, `Storage`, plus the infrastructure-tier ACL modules `AiGateway` and `SpeechGateway`.
- **Each module** has one single-sentence responsibility (no "and"), a published Application-layer contract (its `*Module.php` declaration of commands/queries), and identical internal layering (ADR-003).
- **A minimal `SharedKernel`** holds only identifier Value Objects + the `DomainEvent` contract; a separate `Shared` holds framework plumbing only. Growing the SharedKernel requires two reviewers + an ADR (SAD §16).
- **Cross-module rules (enforced by architecture tests, SDD §5):** no module imports another's `Domain` or `Infrastructure`; no cross-module table joins/relationships; cross-module reference is by *identifier value* only (a `LearnerId` scalar, not an Eloquent relationship).
- **"AI Tutor" guardrail (legacy ADR-003, ratified here too):** no module, package, or namespace named or shaped as an "AI Tutor." The tutor experience is emergent from cooperating contexts.

### Alternatives Considered
1. **Technical-layer-first organization** (`app/Http`, `app/Models`, `app/Services`). Rejected — violates CTO Constitution §7; re-couples every domain.
2. **One module per technical concern** (a "Notifications" module owning all notification logic across domains). Rejected — breaks domain cohesion; the Engagement module owns *habit/engagement* logic, not all delivery.
3. **A large SharedKernel / "Common" module** for cross-cutting entities. Rejected — the classic way modular monoliths quietly re-couple; SharedKernel is kept deliberately starved.
4. **An "AITutor" module.** Rejected (legacy ADR-003 / ADR-004) — the Domain Model is explicit this must not exist.

### Pros
- Modules are genuine boundaries → extraction (ADR-046) is a deployment change, not a rewrite.
- Domain-first layout makes ownership and blast-radius obvious.
- A starved SharedKernel forces real decoupling rather than shared-state convenience.

### Cons
- Deviates from stock Laravel conventions → requires an onboarding note.
- Identifier-by-value across modules means no DB-level cross-module FKs (integrity enforced in code — acceptable per SAD §72).

### Trade-offs
We trade DB-level cross-module referential integrity and stock-Laravel familiarity for **clean extraction seams and genuine modularity** — the properties that determine whether the "modular" in modular monolith is real.

### Consequences
- A new module = new namespace + ServiceProvider + migration prefix + README (SDD §3).
- Cross-module coupling is CI-detectable; the dependency graph is reviewed in architecture review (ADR-054).

### Risks
- **SharedKernel creep** — the most common modular-monolith failure. Mitigated by the two-reviewer + ADR growth gate.
- **Identifier-by-value integrity gaps** if a referenced entity is deleted without cleanup. Mitigated by soft-delete + retention-aware reference handling (ADR-042).

### Migration Strategy
Greenfield. Module splits/merges require an ADR (Domain Model §21).

### Related Documents
CTO Constitution §7; SAD §16; SDD §3, §5, §6; Domain Model §5, §6, §22.

### Future Revisions
- ADR-046 defines which modules extract first (Pronunciation, Content Import) and which last (Core Domain).

---

## ADR-006: Laravel Modular Monolith

| Field | Value |
|---|---|
| **Status** | Accepted |
| **Date** | 2026-07-29 |
| **Related** | ADR-003, ADR-005, ADR-045, ADR-046 |

### Context
The SAD (§8) selects **Modular Monolith First** as the architecture style for the 100–100k-user stages (CTO Constitution §34), internally structured with Clean/Hexagonal per module. The task stack mandates **Laravel 12 / PHP 8.4** as the application runtime. This ADR ratifies the combination: a single Laravel deployable, internally cut along Bounded Context modules, deployable as one unit today and splittable along those same seams later without a rewrite.

### Problem Statement
The default failure mode for a small team building a product expected to reach millions is **premature microservices** — adopting a distributed system before the team can operate one and before the Bounded Context boundaries are proven correct in production (SAD §8). The opposite failure — a tangled single-deployment with no internal seams — makes the eventual split a rewrite. We need a deployment shape that is operationally simple now and split-ready later, on a framework that delivers productivity without sacrificing the seams.

### Decision
**Adopt a Laravel 12 / PHP 8.4 Modular Monolith: one deployable application, internally organized by Bounded Context modules (ADR-005), each Clean/Hexagonal (ADR-003), integrated via Domain Events (ADR-008/ADR-011) even within the monolith so that eventual extraction (ADR-046) is a deployment change, not a logic change.**

- **Laravel 12 / PHP 8.4** as the runtime (mandated stack; selected for team fluency, ecosystem, and PHP 8.4's `readonly` classes that serve Value Objects well).
- **Inertia.js + Vue 3 + TypeScript + Tailwind** as the client (SDD §3) — single deployable serves both API and rendered pages.
- **One application runtime, horizontally scalable, stateless** behind a load balancer; background workers a separately-scaled pool (ADR-020); one PostgreSQL (ADR-021), one Redis (ADR-018/ADR-020), one object store (ADR-023).
- **Split-readiness is the acceptance criterion:** every architectural choice is evaluated against "does this preserve the ability to extract a module into its own service later with a refactor, or does it require a rewrite?" (SAD §1). Choices requiring a rewrite are rejected.

### Alternatives Considered
1. **Microservices from day one.** Rejected — the CTO Constitution (§34) is explicit that architecture follows organizational need, not anticipation; a small MVP team cannot operate a distributed system well, and the boundaries aren't proven yet (Domain Model §22).
2. **A non-Laravel framework (e.g., a "purer" DDD framework).** Rejected — Laravel's ecosystem, hiring pool, and team fluency satisfy the CTO §3 selection criteria (team fluency is priority #1); the purity concerns are solved by Hexagonal Architecture (ADR-003) and the Data Mapper (ADR-010), not by abandoning Laravel.
3. **A monolith without internal seams (classic Laravel).** Rejected — makes extraction a rewrite; defeats the staged-scaling strategy (ADR-045).
4. **Serverless functions as the unit.** Rejected at MVP — operational complexity and cold-start latency concerns for the Core Domain's transactional loop; the modular monolith is simpler to operate (CTO §3).

### Pros
- Single-deployment operational simplicity at MVP — one thing to build, deploy, monitor, back up.
- Fast iteration (CTO Constitution §2, Developer Experience First).
- Split-ready: the Domain-Event integration means a module's extraction is a transport change, not a logic change.
- Laravel's ecosystem accelerates Generic Domain work (Identity, Billing) without compromising the Core Domain (which stays framework-free).

### Cons
- A single deployment means a bug or bad deploy affects the whole system (mitigated by module isolation as blast-radius boundary + feature flags ADR-038).
- Cannot independently scale individual modules until extraction (mitigated by ADR-045 staged plan; background workers already separately scaled).
- PHP/Laravel's single-threaded model requires care for CPU-bound work (delegated to background workers/ASR, ADR-020).

### Trade-offs
We trade independent scaling/deploy cadence (not yet needed) for **operational simplicity and iteration speed now**, while structurally preserving the option to extract later. The optionality is cheap because of the seams; the premature complexity of microservices is expensive. Asymmetry favors the monolith.

### Consequences
- The system deploys as one unit through MVP and likely well past it.
- Extraction is sequenced (ADR-046): Pronunciation and Content Import first (bursty/distinct profiles), Core Domain last.
- All cross-module integration is event-based even in-process (ADR-008), costing slight indirection now to save a rewrite later.

### Risks
- **Premature extraction under "we should be microservices" pressure** — mitigated by ADR-045/046 staging and the requirement of *measured* need.
- **Single-deployment blast radius** — mitigated by module isolation, feature flags, and module-level circuit breakers (ADR-033).

### Migration Strategy
Greenfield. The path *out* of the monolith is ADR-046.

### Related Documents
SAD §1, §8, §10, §53; CTO Constitution §2, §34; SDD §1 (P8), §2; ADR-045, ADR-046.

### Future Revisions
- Extraction triggers are defined in ADR-046; re-evaluate at each SAD §53 stage gate.

---

## ADR-007: CQRS Usage

| Field | Value |
|---|---|
| **Status** | Accepted |
| **Date** | 2026-07-29 |
| **Related** | ADR-002, ADR-009, ADR-010, ADR-011 |

### Context
The SAD (§8, §74) calls for **CQRS applied selectively** — not uniformly. The Domain Model (§10) identified genuine read/write asymmetry specifically in the Learner Model (a detailed write-side Mastery state vs. several distinct read models: `LearnerMasterySummary`, `LearnerMasteryDetail`, `ReviewQueueForLearner`, `TeacherClassroomProgressSummary`). The CTO Constitution (§2) YAGNI principle forbids applying patterns uniformly where they aren't earned.

### Problem Statement
Uniform CQRS (separate command and query stacks everywhere, with projections for every read) adds complexity (a projection pipeline, eventual-consistency reasoning, dual data stores) to every module — including modules (Content Import, Classroom) whose reads are naturally served by their own write tables. Conversely, *no* CQRS leaves the Learner Model's genuinely divergent read patterns served by reconstructing the write aggregate on every read — slow and invariant-enforcing on a read path. We must apply CQRS where it's earned and nowhere else.

### Decision
**Apply CQRS selectively — only where the Domain Model already identified genuine read/write asymmetry (the Learner Model context).**

- **Command/Query separation at the use-case level is universal:** every use case is either a Command (mutating, one aggregate, transactional) or a Query (read, no transaction, returns a ViewModel). This discipline is cheap and applies everywhere (SDD §9).
- **Physical CQRS (denormalized projections fed by Domain Events) applies ONLY to the Learner Model's read models** (`mastery_summary_projection`, `review_queue_projection`), updated asynchronously off events already flowing through the Event Bus (ADR-012) — not a separate sync mechanism.
- **All other modules read directly from their own write-side tables** — no projection infrastructure, no separate read store. The complexity isn't justified (YAGNI).
- The boundary between "needs a projection" and "doesn't" is a review decision (ADR-054); adding a projection to a new context requires demonstrating the read/write divergence.

### Alternatives Considered
1. **Uniform CQRS (every context, every read a projection).** Rejected — violates YAGNI (CTO Constitution §2); doubles the data-sync surface and the eventual-consistency UX burden for modules that don't need it.
2. **No CQRS at all.** Rejected — the Learner Model's hot, divergent reads (review queue, dashboards) would reconstruct the aggregate or do expensive joins on the write path.
3. **Event Sourcing as the write side.** Rejected at MVP — adds significant complexity (snapshots, replay, schema migration of events) for benefit not yet needed; the transactional outbox (ADR-009) already preserves an event stream that *could* feed future event sourcing without committing to it now.

### Pros
- Earned complexity only: the Learner Model gets fast, divergent reads; other modules stay simple.
- Projections reuse the existing Event Bus (ADR-012) — no new sync mechanism.
- Command/Query separation (universal) makes every use case's intent explicit and testable.

### Cons
- Two read patterns coexist (projected vs. direct) — requires the team to know which applies where (documented per module).
- Projections introduce eventual consistency for the Learner Model's read views (a dashboard ~30s stale is an accepted, designed-for trade-off; the write-side Mastery is strongly consistent — SAD §75).

### Trade-offs
We accept a *mixed* read strategy (projected for Core, direct elsewhere) to get the benefits of CQRS where they're real without paying the cost where they're not. Uniformity would be simpler to explain but more expensive to operate.

### Consequences
- Learner Model query handlers read projections; write handlers mutate the aggregate.
- A new read model in Learner Model = a new projection + event handler.
- Other modules' query handlers read their own tables.

### Risks
- **Projection lag** confusing users (a review just completed not yet reflected). Mitigated by short projection refresh + learner-keyed cache invalidation on the causing event (SDD §33).
- **Drift toward uniform CQRS** "for consistency." Mitigated by the "demonstrate divergence" gate (ADR-054).

### Migration Strategy
Greenfield. If a future context develops genuine read/write divergence, add its projection via a new ADR.

### Related Documents
SAD §8, §42, §74; Domain Model §10; CTO Constitution §2; SDD §9, §30.

### Future Revisions
- Event sourcing for the Core Domain remains a *future* option (the outbox stream supports it); not adopted now.

---

## ADR-008: Event Driven Architecture

| Field | Value |
|---|---|
| **Status** | Accepted |
| **Date** | 2026-07-29 |
| **Related** | ADR-006, ADR-009, ADR-011, ADR-012, ADR-046 |

### Context
The SAD (§21) makes Domain Events the integration spine of the modular monolith: modules communicate via events even in-process, specifically so that eventual extraction into services (ADR-046) is a transport change, not a logic change. The Domain Model (§8) defines the canonical event catalog (e.g., `TranscriptReady`, `ReviewSessionCompleted`, `MasteryThresholdReached`, `PronunciationAttemptScored`). The CTO Constitution (§5) requires cross-boundary communication through explicit, versioned contracts, never shared tables or implicit shared state.

### Problem Statement
If modules integrate by direct synchronous calls or shared database tables, three problems arise: (1) a slow/downstream module blocks or corrupts the publisher (tight coupling); (2) extracting a module to a service later requires re-architecting its integrations (a rewrite); (3) the temporal coupling hides failure modes (a downstream consumer failure silently breaks a feature). We need an integration model that is decoupled, versioned, and extraction-ready from day one.

### Decision
**Adopt Event-Driven Architecture for all cross-module integration: modules publish and consume Domain Events via the Event Bus (ADR-012); no module calls another's internals or tables.**

- The event catalog is the Domain Model §8 list, verbatim (Ubiquitous Language preserved end-to-end).
- Events are **past-tense facts** (`ReviewSessionCompleted`, not `UpdateMastery`) — they describe what happened, never command.
- Publishers do not know their consumers (decoupled); consumers react, they do not query the publisher's internal state (Domain Model §6 Conformist/reactive).
- Events flow through the Event Bus abstraction (ADR-012), which is in-process at MVP and broker-backed post-extraction — modules never see the difference.
- Reliability is guaranteed by the Transactional Outbox (ADR-009) for publication and idempotent consumers for reception.

### Alternatives Considered
1. **Synchronous RPC between modules.** Rejected — tight temporal coupling; a slow consumer blocks the publisher; destroys extraction seams (SAD §20).
2. **Shared database tables as integration.** Rejected — the classic monolith coupling; CTO Constitution §5 forbids it; makes extraction a rewrite.
3. **A separate "integration" event vocabulary** distinct from Domain Events.** Rejected — SAD §21 mandates the Domain Model's events verbatim; a second vocabulary fragments the Ubiquitous Language.

### Pros
- Publishers and consumers are decoupled — a downstream module's outage doesn't block the write path (SAD §56).
- Extraction is a transport change: an in-process dispatch becomes a broker message, consumers unchanged (because they're idempotent — ADR-009).
- Events are an auditable, replayable record of system behavior (supports future event sourcing / analytics).
- New consumers (e.g., a future analytics projection) attach without touching publishers.

### Cons
- Eventual consistency across modules must be designed for (a dashboard 30s stale) — acceptable where it doesn't touch the Core Domain's write consistency (SAD §75).
- Debugging an event chain is harder than a call stack — mitigated by correlation IDs and tracing (ADR-033).
- Risk of "event spaghetti" if events are overused for intra-module logic — mitigated by keeping events for *cross-module* integration only.

### Trade-offs
We accept eventual consistency across modules and the debugging indirection of async in exchange for **decoupling, resilience, and extraction-readiness** — the properties that let the system survive a module outage and grow into services without rewrites.

### Consequences
- Every cross-module integration is an event subscription; the Context Map (Domain Model §6) is realized in code as publish/subscribe wiring.
- Consumers must be idempotent (ADR-009) — a discipline enforced in review (ADR-055).
- The Event Bus (ADR-012) and Outbox (ADR-009) become critical infrastructure.

### Risks
- **Non-idempotent consumer** breaking under retry/delivery — mitigated by ADR-009 + idempotency tests (ADR-030).
- **Event-schema breaking changes** rippling across consumers — mitigated by versioning (ADR-051) and the review gate (ADR-054).

### Migration Strategy
Greenfield. The post-extraction broker swap is in ADR-012.

### Related Documents
SAD §20–§22, §56; Domain Model §6, §8; CTO Constitution §5; SDD §35; ADR-009, ADR-011, ADR-012.

### Future Revisions
- Event sourcing (write side) remains a future option; the outbox stream is the foundation.

---

## ADR-009: Transactional Outbox

| Field | Value |
|---|---|
| **Status** | Accepted |
| **Date** | 2026-07-29 |
| **Related** | ADR-002, ADR-008, ADR-011, ADR-012, ADR-021 |

### Context
The SAD (§22, §76) requires that Domain Events be published *within the same transaction* as the state change that caused them — the **transactional outbox pattern** — to avoid the dual-write problem. The CTO Constitution (§45) requires a migration strategy for any data-shape change. This ADR ratifies the outbox as the mechanism guaranteeing no event is lost and no state/event divergence occurs, which is especially critical for the Core Domain: a `ReviewSessionCompleted` event lost means a Mastery update silently unreconciled (Domain Model §16 invariant).

### Problem Statement
The dual-write problem: a handler updates an aggregate *and* dispatches an event. If the dispatch fails, or the process dies between save and dispatch, the state change succeeds but its event is lost (or vice versa) — the system's state and its event-driven projections/consumers diverge silently. For the Core Domain, this means corrupted Mastery reconciliation — among the most damaging defects possible. We need atomic state+event publication.

### Decision
**Adopt the Transactional Outbox pattern: within the handler's transaction, after saving the aggregate, write the event(s) to an `outbox` table in the same transaction; a dedicated `OutboxRelay` worker reads undispatched rows and publishes them to the Event Bus, marking them dispatched.**

- **Atomicity:** aggregate save + outbox insert commit together — one transaction. A crash between them loses nothing (the outbox row persists or rolls back with the save).
- **Relay:** a worker polls `outbox WHERE dispatched_at IS NULL ORDER BY created_at` and publishes; on success marks dispatched (ADR-020 queue/worker).
- **At-least-once delivery** — the relay may publish twice (crash after publish, before marking). Therefore **all consumers are idempotent by design from day one** (SAD §22): each consumer deduplicates by event id.
- **Extraction-ready:** at extraction, the relay publishes to a real broker instead of in-process; consumers are unchanged (they're already idempotent).

### Alternatives Considered
1. **Direct in-process dispatch (`event()->dispatch()`) after save.** Rejected — classic dual-write; loses events on crash; unsafe for the Core Domain.
2. **Two-phase commit (XA) across DB and broker.** Rejected — operationally fragile, poor performance, poor broker support; the outbox is the industry-standard pragmatic alternative.
3. **Change Data Capture (CDC) from DB WAL → broker.** Rejected at MVP — heavier operational footprint (Debezium-class); the outbox table is simpler and sufficient. CDC remains a *future* option at extreme scale.
4. **Maximum-once (fire-and-forget) with reconciliation jobs.** Rejected — reconciliation is reactive, not preventive; the Core Domain needs preventive consistency.

### Pros
- Eliminates the dual-write problem; state and events never diverge.
- At-least-once + idempotent consumers = exactly-once *effect*.
- The outbox table is a natural audit/replay stream (foundation for future event sourcing).
- Extraction swaps only the relay's transport.

### Cons
- Adds an outbox table + relay worker to operate.
- Consumers must be idempotent (discipline cost) — but this discipline is required anyway for broker-readiness.
- Slight latency between commit and consumer notification (relay poll interval) — negligible for async consumers; the synchronous request response is unaffected.

### Trade-offs
We accept one extra table, one worker, and the idempotency discipline in exchange for **guaranteed state/event consistency** — non-negotiable for the Core Domain's integrity and for painless extraction.

### Consequences
- Every mutating handler writes to the outbox within its transaction; the framework's `UnitOfWork` (SDD §9) encapsulates this.
- The relay + outbox are critical infrastructure, monitored as such (ADR-035/036).
- Consumer idempotency is a hard review gate (ADR-055).

### Risks
- **Relay stall** (the worker dies) → events delayed. Mitigated by monitoring outbox depth + alerting (ADR-036); the data is safe (rows persist).
- **Non-idempotent consumer shipped** → double-application on replay. Mitigated by idempotency tests (ADR-030) and review.

### Migration Strategy
Greenfield. At extraction, swap the relay's sink from in-process to broker (ADR-012); no consumer changes.

### Related Documents
SAD §22, §76; CTO Constitution §27, §45; SDD §9, §35; ADR-008, ADR-012, ADR-021.

### Future Revisions
- CDC (Debezium) as an alternative relay at extreme scale, if outbox-table polling becomes a bottleneck.

---

## ADR-010: Repository Pattern

| Field | Value |
|---|---|
| **Status** | Accepted |
| **Date** | 2026-07-29 |
| **Supersedes** | Legacy ADR-011 (Data Mapper pattern) — folded in |
| **Related** | ADR-001, ADR-003, ADR-021, ADR-046 |

### Context
The SAD (§29) mandates the Repository pattern: each aggregate has one repository interface defined in the Domain layer and implemented in Infrastructure. The SDD (§11, §17) and Conflict C-1 (Eloquent Active Record vs. Domain purity) fix the implementation as a **Data Mapper**, not Active Record. This ADR ratifies the Repository pattern and records the Data Mapper decision (legacy ADR-011) as its implementation.

### Problem Statement
If the Application layer loads/persists aggregates through Eloquent directly, the Domain becomes coupled to Eloquent (Conflict C-1), the Core Domain loses purity and testability, and a database swap or extraction becomes a system-wide refactor. We need a persistence abstraction that lets use cases load/save aggregates without knowing the storage, and that keeps Eloquent confined to Infrastructure.

### Decision
**Adopt the Repository pattern with the Data Mapper implementation: one repository interface per aggregate (port, in Domain), implemented by an Eloquent-backed Data Mapper (adapter, in Infrastructure); the Application layer depends only on the interface.**

- **One repository per aggregate** (Domain Model §11): `ReviewSessionRepository`, `LearnerModelRepository`, etc.
- **Ports return/accept Domain objects, never Eloquent models.** A repository's `save()` uses a Data Mapper to translate the aggregate ↔ Eloquent row set within the caller's transaction.
- **Read repositories are separate** for queries/selective CQRS (ADR-007) — they return flat ViewModels/Value Objects, not aggregates, avoiding invariant enforcement on read paths.
- **Whole-aggregate persistence** within one transaction (one aggregate per transaction, SAD §76).
- **Optimistic concurrency** (version columns) on Core Domain aggregates — concurrent `ReviewSessionCompleted` events cannot silently drop a Mastery update (Domain Model §11).
- **Eloquent is confined to Infrastructure** (Conflict C-1 resolution, per SAD §25) — no Eloquent model crosses into Application or Domain.

### Alternatives Considered
1. **Active Record (Eloquent models as Domain Entities).** Rejected — Conflict C-1; couples Domain to Eloquent, destroys purity/testability/swap/extract seams. The most common Laravel DDD failure.
2. **Generic `Repository<T>` base with dynamic query scopes.** Rejected — hides intent, invites cross-module table access, defeats the one-aggregate-per-repository clarity.
3. **No repository; handlers query Eloquent directly.** Rejected — same coupling as #1; untestable Core Domain.
4. **CQRS without repositories (event-sourced aggregates).** Rejected at MVP — see ADR-007.

### Pros
- Domain is pure and unit-testable with zero infrastructure.
- A database swap or extraction is contained to a new repository adapter.
- Whole-aggregate persistence + optimistic locking protect Core Domain consistency.
- Clear ownership: one repository per aggregate.

### Cons
- Data Mapper boilerplate per aggregate (mitigated by the round-trip test, SDD §17).
- Loses Eloquent's ergonomics in Domain — by design.

### Trade-offs
We accept mapper boilerplate and the loss of Eloquent ergonomics in the Domain in exchange for **purity, testability, and swap/extract seams** — resolving Conflict C-1 per SAD §25.

### Consequences
- Eloquent models are `internal` to Infrastructure; the Domain defines ports only.
- A future store swap (e.g., event-sourced Core at extreme scale) is a new mapper implementation.
- The round-trip property (`toDomain(toEloquent(a)) == a`) is a mandatory test (ADR-030).

### Risks
- **A repository leaking Eloquent models** into Application — mitigated by return-type contracts + review.
- **Partial-aggregate saves** leaving inconsistency — mitigated by whole-aggregate save discipline.

### Migration Strategy
Greenfield. A store swap replaces the adapter only.

### Related Documents
SAD §25, §29, §72; CTO Constitution §45; SDD §11, §17 (Conflict C-1); ADR-003, ADR-007.

### Future Revisions
- Event-sourced repository for the Core Domain remains a future option at extreme scale.

---

## ADR-011: Domain Events

| Field | Value |
|---|---|
| **Status** | Accepted |
| **Date** | 2026-07-29 |
| **Related** | ADR-008, ADR-009, ADR-012 |

### Context
The Domain Model (§8) defines the canonical Domain Event catalog; the SAD (§21) makes events the integration spine. Domain Events are the Ubiquitous-Language-preserving mechanism that carries business meaning across module and service boundaries (Domain Model §21 — terms don't drift; events version like APIs).

### Problem Statement
Without a disciplined Domain Event model, integration events either (a) leak infrastructure/provider shapes (violating the ACL principle, Domain Model §19), (b) drift in naming/meaning from the Ubiquitous Language, or (c) become commands in disguise ("UpdateMastery") that recreate tight coupling. We need an event model that is business-meaningful, language-faithful, and versioned.

### Decision
**Adopt the Domain Model §8 event catalog verbatim as the system's Domain Events, with these rules:**

- Events are **plain PHP objects** in the producing module's `Domain\Events\`, implementing the SharedKernel `DomainEvent` contract.
- **Past-tense facts** about something that happened (`ReviewSessionCompleted`, `TranscriptReady`, `PronunciationAttemptScored`) — never commands.
- **Provider/infrastructure-shape-free** — an event carries LexiFlow-domain fields only; no raw provider response survives the ACL (Domain Model §19). The AI/Speech Gateways (ADR-013/016) translate at the boundary.
- **Ownership is fixed** (SDD §6 table): each event has exactly one producing module.
- **Versioned** (ADR-051): additive changes are non-breaking; a breaking shape change is a deliberate, reviewed, cross-consumer event.
- Published via the Transactional Outbox (ADR-009); consumed idempotently (ADR-008).

### Alternatives Considered
1. **Thin wrappers around provider/queue messages.** Rejected — leaks infrastructure shapes; violates the ACL.
2. **Command-style integration messages ("UpdateMastery").** Rejected — recreates temporal coupling; events are facts, not commands (Domain Model §9 separates Commands from Events).
3. **A separate "integration event" layer mapping Domain Events to transport shapes.** Rejected at MVP — premature; the Domain Events serve transport directly. (May be revisited at extraction if transport needs differ.)

### Pros
- Business meaning travels intact across boundaries; the Ubiquitous Language is preserved end-to-end (Domain Model §21).
- Provider swaps don't ripple into event shapes (ACL isolation).
- Events are auditable, replayable, and extraction-ready.

### Cons
- Requires discipline to keep events as facts and to version changes carefully.
- Event proliferation if used for intra-module logic (mitigated: events are for *cross-module* integration only).

### Trade-offs
We accept the discipline of faithful, versioned, past-tense events in exchange for **language integrity, ACL isolation, and a stable integration contract**.

### Consequences
- The event catalog is the authoritative integration contract; the Event Bus registry is generated from it (SDD §35).
- Adding/renaming an event touches the catalog + producer + all consumers + docs (review gate ADR-054).

### Risks
- **Command-disguised-as-event** creeping in — mitigated by naming review (past-tense).
- **Unversioned breaking change** — mitigated by ADR-051 + ADR-054.

### Migration Strategy
Greenfield.

### Related Documents
Domain Model §8, §19, §21; SAD §21, §70; SDD §6, §35; ADR-008, ADR-009, ADR-012, ADR-051.

### Future Revisions
- An integration-event mapping layer at extraction, if broker transport shapes diverge from Domain Events.

---

## ADR-012: Event Bus

| Field | Value |
|---|---|
| **Status** | Accepted |
| **Date** | 2026-07-29 |
| **Supersedes** | Legacy ADR-014 (message broker) — folded in |
| **Related** | ADR-008, ADR-009, ADR-011, ADR-020, ADR-046 |

### Context
The SAD (§21–§22) defines an in-process Event Bus abstraction at MVP, backed by a real message broker underneath so the abstraction is correct for the post-extraction world — modules interact only with the Bus interface, never knowing whether dispatch is in-process or brokered. The task stack includes Redis. This ADR ratifies the Event Bus abstraction and records the underlying transport decision (legacy ADR-014).

### Problem Statement
If modules dispatch events directly to an in-process dispatcher, extraction to a broker later requires every publisher and consumer to change transport code. If they dispatch directly to a broker from day one, the MVP incurs distributed-systems operational complexity it doesn't yet need (CTO Constitution §34). We need a transport-agnostic Bus that is in-process now and broker-backed later, with a concrete, simple underlying transport at MVP.

### Decision
**Adopt a single Event Bus interface (`Shared\Bus\EventBus`) that all publishers/consumers use; back it with an in-process, transactionally-consistent dispatch at MVP (driven by the Outbox relay, ADR-009), with Redis as the underlying mechanism — and design the interface so that swapping to a real distributed broker at extraction changes only the Bus implementation, not any publisher/consumer.**

- **The interface is the contract:** `$bus->publish($event)` and subscription registration. No module references the transport.
- **MVP transport:** the Outbox relay (ADR-009) reads outbox rows and dispatches to registered in-process consumers. Redis underpins both the queue (ADR-020) and can serve as the broker primitive if a broker is needed before a dedicated one is justified.
- **At extraction (ADR-046):** the Bus implementation is swapped to a real broker (e.g., SNS+SQS/Kafka) for extracted modules; publishers/consumers are unchanged because they depend only on the interface, and consumers are already idempotent (ADR-009).
- **Dedicated broker selection** (legacy ADR-014) is deferred to the extraction stage — a contained, Bus-implementation-only decision then, not a system-wide one now.

### Alternatives Considered
1. **Direct broker from day one (Kafka/SNS+SQS).** Rejected at MVP — distributed-systems operational complexity not yet earned (CTO §34); the in-process Bus with outbox is correct and simpler.
2. **Direct in-process dispatch with no abstraction.** Rejected — extraction would touch every publisher/consumer.
3. **A bespoke pub/sub framework.** Rejected — Laravel's event system + the outbox relay provide the mechanism; a bespoke framework is unearned complexity.
4. **PHP-specific async runtimes (Swoole/RoadRunner) as the bus.** Rejected at MVP — operational complexity; the request/worker model suffices.

### Pros
- One interface now and post-extraction; zero publisher/consumer rewrite at extraction.
- MVP operational simplicity (in-process + Redis) with a correct extraction path.
- The outbox-driven Bus guarantees reliability (ADR-009).

### Cons
- An abstraction layer to maintain (small; the interface is stable).
- The choice to defer the dedicated broker means the Bus implementation has two modes to keep correct.

### Trade-offs
We accept a thin abstraction and a deferred (but designed-for) broker decision in exchange for **extraction-readiness without premature distributed-systems complexity**.

### Consequences
- The Bus is critical infrastructure; monitored (ADR-035/036).
- The dedicated broker ADR is a future, contained decision (legacy ADR-014, deferred).

### Risks
- **Abstraction leak** (a module reaching past the Bus to a transport) — mitigated by the interface contract + review.
- **Premature broker adoption** under "we should be more scalable" pressure — mitigated by ADR-045 staging.

### Migration Strategy
Greenfield. Broker swap at extraction changes the Bus implementation only.

### Related Documents
SAD §21, §22; SDD §35; ADR-008, ADR-009, ADR-020, ADR-045, ADR-046.

### Future Revisions
- Dedicated broker selection (SNS+SQS vs Kafka vs Redis Streams) at the extraction stage, evaluated against CTO §3.

---

## ADR-013: AI Gateway

| Field | Value |
|---|---|
| **Status** | Accepted |
| **Date** | 2026-07-29 |
| **Related** | ADR-003, ADR-014, ADR-015, ADR-018, ADR-019, ADR-058 |

### Context
The SAD (§33–§34) makes the **LLM Gateway** the single ACL through which all generative-AI calls flow, because AI inference cost is the dominant scaling risk (CTO Constitution final review; Product Strategy §44). The Domain Model (§19) requires every external AI provider be accessed through an Anti-Corruption Layer translating provider shapes into LexiFlow's Ubiquitous Language. The SDD (§38) details the Gateway's responsibilities: provider selection/fallback, prompt invocation, request/response translation, caching integration, rate limiting, cost tracking, streaming.

### Problem Statement
If modules (Linguistic Analysis, Pronunciation) call LLM provider SDKs directly, four problems emerge: (1) provider-specific logic and prompts scatter across modules, duplicating logic (CTO Constitution §45 duplication rule); (2) a provider swap becomes a multi-module refactor; (3) cost control (rate limiting, tiered models, cache-only fallback) has no single enforcement point; (4) provider response shapes leak into the Domain, violating the ACL. We need a single chokepoint that owns provider interaction, cost control, and ACL translation.

### Decision
**Adopt the AI Gateway as the single internal component through which all text-LLM requests flow; no module outside the Gateway calls an LLM SDK directly (hard, CI-enforced rule).**

- **Single ACL:** Linguistic Analysis/Pronunciation ask in LexiFlow-domain terms (`generateExplanation`, `generateTranslation`); the Gateway translates to provider requests and provider responses back to LexiFlow Value Objects. No raw provider object crosses the boundary.
- **Responsibilities (SAD §34):** provider selection + fallback (ADR-014), prompt invocation (ADR-015), ACL translation, caching integration (the shared content cache sits *in front* of the Gateway, ADR-019), per-tier rate limiting, per-request cost tracking, streaming support (built from day one for future conversation/progressive rendering).
- **Separate from the Speech Gateway** (ADR-016): text-LLM and audio/ASR have different provider landscapes, cost/latency profiles, and the flagged pronunciation scope uncertainty — two ACLs, not one.
- **Tiered model selection (SAD §38):** the Gateway classifies requests and routes simple calls to cheaper/faster models, reserving stronger models for nuanced idiom/grammar explanation.
- **Cache-only circuit breaker (SAD §55):** on provider degradation or cost anomaly, serve cached responses, queue/honestly-fail new requests; never fail open to unlimited spend.

### Alternatives Considered
1. **Direct provider calls from modules.** Rejected — duplicates logic, scatters cost control, leaks provider shapes, makes swaps multi-module (the exact failures above).
2. **A shared "provider client" library used ad hoc.** Rejected — SAD §80; recreates coupling the ACL pattern prevents; no single cost-control point.
3. **One combined AI+Speech Gateway.** Rejected (SAD §40) — audio has a genuinely different provider landscape and the pronunciation scope uncertainty; isolation is the containment.
4. **A managed "LLM router" SaaS as the Gateway.** Rejected at MVP — adds a vendor dependency and cost for logic we can own; the Gateway's cost-control and caching logic is the company's IP-adjacent capability.

### Pros
- Provider swap, new fallback, cost-policy change, prompt change: all happen in one place.
- Cost control (rate limits, tiered models, cache-only fallback) is centralized and enforceable.
- The Domain never sees a provider shape (ACL integrity).
- A single place to instrument cost (ADR-058).

### Cons
- One more component/hop on the AI path (mitigated: cache sits in front, so most requests never reach it).
- The Gateway becomes critical infrastructure (a Gateway outage affects all AI features) — mitigated by cache-only fallback + independent scaling.

### Trade-offs
We accept a centralized critical component in exchange for **single-point provider/cost control and ACL integrity** — the alternative (scattered direct calls) is strictly worse for a system whose dominant risk is AI cost.

### Consequences
- The "no LLM SDK outside the Gateway" rule is CI-enforced (architecture tests flag provider SDK imports outside `AiGateway`).
- The Gateway is independently scalable and rate-limited (SAD §10); the `ai` queue feeds cache-miss jobs (ADR-020).
- Cost instrumentation lives here (ADR-058).

### Risks
- **Gateway outage** → all AI features degrade. Mitigated by cache-only fallback (cached explanations still served) + graceful honest failure.
- **Tiered-model misclassification** → wrong model for a request (cost or quality). Mitigated by classification tests (ADR-030).

### Migration Strategy
Greenfield. Provider swaps change only the Gateway's adapter layer.

### Related Documents
SAD §33, §34, §38, §55, §80; Domain Model §19; SDD §38; Product Strategy §44; ADR-014, ADR-015, ADR-019, ADR-058.

### Future Revisions
- Embedding/RAG capability (SAD §36) as a new Gateway method; conversation streaming (V3) uses the built-in streaming support.

---

## ADR-014: LLM Provider Strategy

| Field | Value |
|---|---|
| **Status** | Accepted (Conditional) |
| **Date** | 2026-07-29 |
| **Supersedes** | Legacy ADR-013 (LLM provider + fallback) — renumbered |
| **Related** | ADR-013, ADR-015, ADR-016, ADR-058 |

### Context
The SAD (§81, §88) left the specific LLM provider(s) and fallback model open, noting they depend on real usage assumptions the (still-unwritten) GTM plan would inform, and that AI provider market volatility makes provider-agnostic contracts essential. The CTO Constitution (§3) selection framework prioritizes team fluency, operational maturity, total cost of ownership, reversibility, and ecosystem health. The Gateway (ADR-013) is provider-agnostic by design, so the provider decision is contained to the Gateway's adapter layer.

### Problem Statement
The LLM provider decision governs the system's largest variable cost and a trust-critical capability (Bangla translation/explanation quality — CEO Vision §7). The provider market is volatile (pricing, capability, availability change rapidly). A wrong primary choice or a missing fallback can mean either runaway cost, quality regressions, or outage-induced feature failure. We must select a primary provider and a fallback strategy that balance quality (especially Bangla), cost, reliability, and reversibility — while remaining honest that final tuning depends on usage data not yet available.

### Decision
**Adopt a multi-provider strategy behind the AI Gateway (ADR-013): a primary provider for general explanation/translation, a stronger-tier model reserved for nuanced idiom/grammar work (tiered selection), and a configured fallback provider for the primary on degradation/outage. Specific vendors are selected via the CTO §3 framework and treated as swappable — the Gateway's provider-agnostic contract makes a swap a contained adapter change.**

- **Strategy over specific vendor lock-in:** the decision's *binding* content is the multi-provider + tiered + fallback *architecture*, not a single vendor name. Vendor identities are configuration (ADR-039) evaluated against CTO §3, refreshed as the market moves, and captured in a short companion decision note kept current.
- **Selection criteria applied:** Bangla translation/explanation quality (verified against the eval set, ADR-015), cost per million tokens, latency, reliability/SLA, data-handling terms (no training on LexiFlow data — privacy, ADR-041), ecosystem/API stability, reversibility (the Gateway abstraction guarantees this).
- **Fallback:** on primary degradation/outage, the Gateway routes to the fallback provider; if both fail, the cache-only circuit breaker serves cached responses and honestly fails new requests (ADR-013).
- **Conditional:** final per-vendor confirmation and tiered-model thresholds await real usage data (volume, content diversity → cache-hit rate) from the GTM plan / early MVP traffic. The architecture is not blocked by this.

### Alternatives Considered
1. **Single provider, no fallback.** Rejected — single point of failure; unacceptable given provider volatility and the trust-criticality of always-available explanation.
2. **Self-hosted open-weight models.** Rejected at MVP — operational expertise the team doesn't have (CTO §3 rejection criterion); GPU cost/ops burden before scale justifies it. Remains a *future* option at scale for the cheapest tier.
3. **Always-strongest-model.** Rejected — cost-prohibitive; ignores the tiered-selection cost lever (SAD §38).
4. **Always-cheapest-model.** Rejected — quality regressions on nuanced Bangla idiom/grammar (trust failure, CEO Vision §7).

### Pros
- Multi-provider + fallback = resilience to provider outage/volatility.
- Tiered selection = real cost optimization without quality sacrifice.
- Provider-agnostic Gateway = a swap is contained and cheap.
- Bangla quality is a first-class selection criterion (not an afterthought).

### Cons
- Multi-provider means integrating/testing more than one adapter.
- Conditional on usage data for final vendor/threshold tuning.

### Trade-offs
We accept multi-provider integration complexity in exchange for **resilience, cost optimization, and reversibility** in the system's largest cost center — the correct trade given provider market volatility.

### Consequences
- Provider identities are configuration, not code; swapping updates config + the adapter, nothing else.
- Vendor changes run against the Bangla eval set (ADR-015) before rollout.
- Cost per provider/model is instrumented (ADR-058).

### Risks
- **Vendor pricing/availability shock** — mitigated by fallback + reversibility.
- **Bangla quality regression on a swap** — mitigated by the eval-set gate (ADR-015).
- **Data-handling terms change** (a provider starts training on inputs) — mitigated by the privacy contract requirement (ADR-041) + the swap path.

### Migration Strategy
Swapping a provider = new adapter + config + eval-set pass; no Domain/Application change.

### Related Documents
SAD §34, §38, §81, §88; CTO Constitution §3; SDD §38; Product Strategy §44; ADR-013, ADR-015, ADR-041, ADR-058.

### Future Revisions
- Self-hosted open-weight models for the cheapest tier once scale and team expertise justify it.
- Re-evaluate vendor selection quarterly against the moving market.

---

## ADR-015: Prompt Versioning

| Field | Value |
|---|---|
| **Status** | Accepted |
| **Date** | 2026-07-29 |
| **Related** | ADR-013, ADR-014, ADR-019, ADR-030 |

### Context
The SAD (§35) treats prompt templates as **versioned artifacts** reviewed with code-change rigor, because a prompt change that degrades Bangla translation quality is exactly the regression the CEO Vision trust promise cannot tolerate (CEO Vision §7). Prompts are tagged to domain concepts (Explanation generation, Translation generation), not to providers, so a provider swap doesn't rewrite the prompt library.

### Problem Statement
If prompts are inline strings scattered through code, four problems emerge: (1) a quality regression from a prompt change is invisible until users complain (too late for a trust-sensitive product); (2) the shared content cache (ADR-019) keys must invalidate on prompt/model changes — without versioning, stale (possibly worse) explanations serve after a swap; (3) prompts drift across call sites; (4) a provider swap forces a prompt-library rewrite if prompts are provider-coupled. We need prompts as first-class, versioned, reviewable, eval-gated artifacts.

### Decision
**Adopt prompt templates as versioned artifacts: stored separately from code, tagged to domain concepts (not providers), every change gated on a before/after evaluation against a fixed Bangla-quality eval set, and the version included in the shared-content-cache key (ADR-019) so a prompt/model change correctly invalidates.**

- **Artifacts, not inline strings:** prompts live in a versioned store/repo, referenced by the Gateway by template id + version.
- **Domain-tagged:** a template serves a domain concept (Explanation, Translation); provider-specific framing lives only in the Gateway's adapter, not the template.
- **Eval-gated changes:** every prompt change runs against the native-speaker-validated Bangla eval set (ADR-030); results accompany the PR; a regression blocks merge.
- **Cache-coupled versioning:** the content-cache key includes `prompt_template_version` + `model_version` (ADR-019) — a swap that should change outputs correctly invalidates; nothing stale serves.
- **Rollback:** a prompt version is immutable; rolling back = repointing to the prior version (safe, instant).

### Alternatives Considered
1. **Inline prompt strings in code.** Rejected — invisible regressions; cache-invalidation impossible; drift.
2. **Provider-coupled prompts.** Rejected — a swap rewrites the library.
3. **Prompts without eval gating** ("we'll review the diff").** Rejected — a diff doesn't reveal quality regression; the eval set is the objective gate (trust-critical).
4. **Manual quality checks post-deploy.** Rejected — reactive, too slow for a trust-sensitive product; users hit the regression first.

### Pros
- Quality regressions caught pre-merge (objective eval gate).
- Cache correctness on prompt/model changes (versioned keys).
- Provider swaps don't touch the prompt library.
- Instant, safe rollback.

### Cons
- An eval set to build and maintain (native-speaker involvement — the right cost for a trust-critical capability).
- Slight process overhead per prompt change (justified).

### Trade-offs
We accept eval-set maintenance and process overhead in exchange for **preventing the single most trust-damaging class of regression** (silent Bangla quality decay) — directly serving CEO Vision §7.

### Consequences
- Prompt changes are reviewed like code, with evidence.
- The cache key scheme depends on this versioning (ADR-019).
- Translation-correction feedback (teachers, ADR-folded §40 SDD) feeds eval-set improvement.

### Risks
- **Eval set drift/coverage gaps** missing a real regression. Mitigated by expanding the eval set from real corrections (SDD §40/§45).
- **Forgetting to bump version on a change** → stale cache. Mitigated by tooling (version is auto-derived from template hash).

### Migration Strategy
Greenfield. The eval set grows over time.

### Related Documents
SAD §35; CEO Vision §7; SDD §38, §40; ADR-013, ADR-014, ADR-019, ADR-030.

### Future Revisions
- Automated LLM-as-judge evals to reduce manual load as volume grows (ADR-030).

---

## ADR-016: Speech Recognition Provider

| Field | Value |
|---|---|
| **Status** | Accepted (Conditional) |
| **Date** | 2026-07-29 |
| **Supersedes** | Legacy ADR-002 (provider half) — renumbered |
| **Related** | ADR-013, ADR-017, ADR-020, ADR-039 |

### Context
The SAD (§40) isolates speech/pronunciation behind a **separate Speech Gateway** ACL (distinct from the text-LLM Gateway) because audio processing has a different provider landscape, cost/latency profile, and the flagged pronunciation scope uncertainty (CTO Constitution §0). The Domain Model (§5, §19) requires the ACL translate provider shapes to LexiFlow's `PronunciationScore` Value Object, stripping provider-specific detail. The CTO Constitution (§3, §0) flags the ASR provider landscape as less mature and more volatile than text-LLM, and notes the provider decision depends on GTM usage assumptions not yet available.

### Problem Statement
Pronunciation scoring needs an ASR/pronunciation-capable provider, but this provider category is more volatile and less mature than text-LLM providers, with quality directly affecting learners (bad pronunciation feedback is *actively harmful*, not just unhelpful — CTO §0). We must select a provider strategy that is isolated (so its volatility can't destabilize the rest), swappable, and honest about its conditional dependence on usage data.

### Decision
**Adopt the isolated Speech Gateway (separate from the text Gateway) as the single ACL for ASR/pronunciation, with a primary provider selected via the CTO §3 framework and a fallback path; the Gateway's contract (`scoreAttempt → PronunciationScore`) is provider-agnostic and scope-agnostic (serves both binary-v0 and full scoring, ADR-017), so the provider choice is a contained, swappable adapter decision.**

- **Isolation (SAD §40):** the Speech Gateway is its own component; Pronunciation is the first extraction candidate (ADR-046) precisely because of this isolation.
- **Provider selection (conditional):** a primary ASR/pronunciation provider selected against CTO §3 (quality on Bangla-accented English — critical, since the target persona is Bangla L1; cost; latency vs. the <3s budget, CTO §25; reliability; data terms, ADR-041). Specific vendor identity is configuration, refreshed as the volatile market moves.
- **Swappability:** a provider swap changes only the Gateway's adapter; the domain contract is unchanged.
- **Conditional on GTM/usage data:** final vendor confirmation and the full-vs-v0 scope (ADR-017) await real usage signal; the architecture is not blocked.

### Alternatives Considered
1. **Couple pronunciation into the text-LLM Gateway.** Rejected — loses isolation; ASR's distinct profile contaminates text cost/latency control.
2. **Self-hosted ASR (Whisper-class).** Rejected at MVP — operational/GPU complexity the team lacks (CTO §3); a future option at scale for the cheapest tier.
3. **Single provider, no fallback.** Rejected — single point of failure in a volatile category.
4. **Provider-specific score surfaces in the Domain.** Rejected — violates the ACL; a swap would ripple.

### Pros
- Isolation contains the system's most volatile external dependency.
- Swappability protects against provider market churn.
- The scope-agnostic contract (ADR-017) lets the MVP-scope decision reverse without rearchitecture.
- Bangla-accented English quality is a first-class selection criterion.

### Cons
- Two AI ACLs to maintain (text + speech) — justified by their distinct profiles.
- Conditional on usage data for final vendor tuning.

### Trade-offs
We accept a second ACL and conditional vendor status in exchange for **isolation and swappability of the most volatile dependency** — the correct trade given the CTO's explicit risk flag.

### Consequences
- The Speech Gateway shares the circuit-breaker/cost-tracking pattern with the text Gateway, with ASR-specific thresholds.
- Pronunciation is extraction-ready first (ADR-046).
- Provider identities are configuration (ADR-039).

### Risks
- **ASR quality on Bangla-accented English** insufficient → harmful feedback. Mitigated by quality gating in selection + the simplified v0 (ADR-017) reducing harm surface.
- **Provider volatility** → outage/cost shock. Mitigated by fallback + isolation + graceful degradation (SAD §83).

### Migration Strategy
Swapping a provider = new adapter + config; no Domain change. Extraction (ADR-046) moves the Gateway with the Pronunciation module.

### Related Documents
SAD §40, §50, §53, §83; CTO Constitution §0, §3; Domain Model §5, §19; SDD §39; ADR-013, ADR-017.

### Future Revisions
- Self-hosted ASR for the cheapest tier at scale; re-evaluate vendor against the volatile market.

---

## ADR-017: Pronunciation MVP Scope

| Field | Value |
|---|---|
| **Status** | Accepted |
| **Date** | 2026-07-29 |
| **Supersedes** | Legacy ADR-002 (scope half) — renumbered |
| **Related** | ADR-016, ADR-038, ADR-050 |

### Context
The CTO Constitution (§0) flags pronunciation scoring in MVP as the **single highest engineering-risk item**, explicitly recommending an ADR to "pressure-test whether a simpler v0 (binary 'close enough / needs work' scoring, rather than granular phoneme-level feedback) is a better MVP cut than the full feature." The Product Strategy (§16) moved pronunciation into MVP (a defensible product call, but "not a free engineering decision"). The SAD (§40) isolated the component specifically so this scope decision could be made — and reversed — without entangling the rest.

### Problem Statement
Full phoneme-level pronunciation scoring is materially harder than binary scoring: it requires a higher-quality ASR provider, more sophisticated feedback generation, and a higher quality bar (granular wrong feedback is more harmful than coarse wrong feedback). Shipping the full feature at MVP risks: burning the team's capacity on the riskiest feature before the core loop is validated; choosing a provider prematurely (ADR-016); and harming learners with confidently-wrong granular feedback. We must decide the MVP scope of pronunciation.

### Decision
**Ship a simplified v0 pronunciation feature at MVP: binary/threshold scoring ("close enough / needs work") behind a feature flag (ADR-038), with a documented path to full phoneme-level scoring as a future revision. This directly follows the CTO Constitution §0 recommendation.**

- **v0 scope:** a Shadowing Session records the learner's audio; the Speech Gateway (ADR-016) returns a binary/threshold `PronunciationScore` with minimal, safe feedback; the result feeds the Learner Model as a speech-production Mastery signal.
- **Behind a feature flag (ADR-038):** rollout is gradual; cost/quality observed in production (SAD §69); the open provider decision (ADR-016) resolves in production reality, not purely in planning.
- **Scope-agnostic contract:** the Speech Gateway's contract (`scoreAttempt → PronunciationScore`) serves both v0 and full; only the adapter + score components change at upgrade. No rearchitecture.
- **Logged as deliberate technical debt** (ADR-050): the gap between v0 and full is recorded with what was deferred and what upgrading requires.

### Alternatives Considered
1. **Full phoneme-level scoring at MVP.** Rejected — the highest-risk feature before the core loop is validated; premature provider commitment; higher harm surface from granular wrong feedback. The CTO §0 recommendation explicitly favors v0.
2. **Defer pronunciation entirely to V2.** Rejected — Product Strategy §16 moved it into MVP for good reason (it's a "why not ChatGPT" differentiator, Product Strategy §12); the v0 captures that value at manageable risk.
3. **v0 with no flag (hard-launched).** Rejected — loses the ability to observe and reverse; the flag is the containment (SAD §69).

### Pros
- Captures pronunciation's differentiation value (vs. ChatGPT) at manageable risk.
- Reduces the harm surface (coarse feedback is safer than granular wrong feedback).
- Resolves the provider decision (ADR-016) in production, not on speculation.
- Reversible/upgradeable without rearchitecture (scope-agnostic contract).

### Cons
- v0 is less impressive in demos than full scoring (acceptable; honesty over demo-polish, CEO Vision §4).
- Logged debt must be paid (upgraded) eventually (ADR-050).

### Trade-offs
We accept a less-polished v0 and deliberate technical debt in exchange for **de-risking the system's highest-risk MVP feature** — the correct trade per CTO Constitution §0, which this ADR ratifies.

### Consequences
- Pronunciation ships behind a flag at MVP with binary scoring.
- The upgrade to full scoring is a future ADR (superseding this one) + a Speech Gateway adapter change.
- v0 limitations are documented debt (ADR-050).

### Risks
- **v0 quality still harmful** (even binary feedback wrong). Mitigated by quality gating + the flag (can disable).
- **Forgetting to upgrade** (debt forgotten). Mitigated by ADR-050 tracking.

### Migration Strategy
v0 → full = new adapter + score components + eval gate; contract unchanged. The flag enables the full path per-cohort.

### Related Documents
CTO Constitution §0, §48; Product Strategy §12, §16; SAD §40, §69; SDD §39; ADR-016, ADR-038, ADR-050.

### Future Revisions
- Supersede with full-scoping ADR once v0 validates demand and a quality provider is confirmed.

---

## ADR-018: Cache Strategy

| Field | Value |
|---|---|
| **Status** | Accepted |
| **Date** | 2026-07-29 |
| **Related** | ADR-019, ADR-021, ADR-033, ADR-058 |

### Context
The CTO Constitution (§26) names caching the **single highest-leverage decision for both performance and cost**, with a cardinal rule: clearly separate the *shared content cache* (learner-independent) from the *personalized cache* (learner-specific). The SAD (§31) realizes this as two distinct Redis caches. Product Strategy (§44) makes the content-cache-driven cost curve the business's core unit-economics strategy. Conflating the two caches — over-caching (destroys personalization) or under-caching (destroys the cost curve) — is treated as a correctness bug.

### Problem Statement
A single undifferentiated cache creates two failure modes: (1) keying personalized data (a review queue) on shared keys leaks one learner's state to another (privacy violation + wrong data); (2) keying shared content (an explanation) on learner keys destroys reuse (every learner re-pays the AI cost) — collapsing the cost flywheel. We need a cache strategy with a hard separation, distinct invalidation, and version-correct keys.

### Decision
**Adopt two strictly-separated Redis caches: a shared Content Cache (learner-independent, long TTL, content-keyed) and a Learner Cache (personalized, short TTL, learner-keyed, event-invalidated), never sharing infrastructure, keys, or invalidation logic.**

- **Content Cache (ADR-019):** for Explanations/Translations (Value Objects, Domain Model §13); key = `content_hash + L1 + prompt_version + model_version`; long TTL; invalidates only on version/quality change; sits in front of the AI Gateway (a hit never reaches a provider).
- **Learner Cache:** for personalized read models (review queue, mastery summary); key = `learner_id + read_model + params`; short TTL; event-driven invalidation (e.g., on `ReviewSessionCompleted`).
- **Separation enforced structurally:** distinct cache wrapper classes (`ContentCache`, `LearnerCache`) with distinct key builders; a linter flags any `learner_id` in a content-cache key.
- **Cache-only circuit breaker:** on provider degradation/cost anomaly, serve cached content, honestly-fail new requests (ADR-013).

### Alternatives Considered
1. **Single undifferentiated cache.** Rejected — the two failure modes above; CTO §26 explicitly forbids conflating.
2. **Content cache only (no learner cache).** Rejected — personalized reads (review queue) would hit the DB/projection every time, missing a cheap latency win.
3. **Learner cache with long TTL.** Rejected — personalized data goes stale (a just-completed review not reflected); short TTL + event invalidation is correct.
4. **In-process memory cache instead of Redis.** Rejected for the content cache (not shared across instances); acceptable only as a second tier at high scale.

### Pros
- Content cache delivers the cost flywheel ($0 marginal cost per reuse) and the sub-3s latency path (mostly hits).
- Learner cache gives fast personalized reads without staleness.
- Structural separation prevents the conflation bug class.

### Cons
- Two caches to operate and reason about.
- Version-key discipline required (a missed version bump serves stale content).

### Trade-offs
We accept two-cache operational complexity in exchange for **both** the cost flywheel **and** correct personalization — the alternative forces a choice between cost and correctness, and the business needs both.

### Consequences
- Cache-hit rate is a first-class business metric (ADR-058).
- Prompt/model changes must bump cache-key versions (ADR-015).
- The separation is a review gate (ADR-055).

### Risks
- **Conflation bug** (a learner_id leaking into a content key). Mitigated by linter + distinct key builders.
- **Stale content after a quality change** (missed version bump). Mitigated by auto-derived version from template/model hash.

### Migration Strategy
Greenfield. A two-tier (in-process + Redis) content cache at high scale is a future optimization.

### Related Documents
CTO Constitution §26; SAD §31, §38, §55; Product Strategy §44; SDD §33; ADR-015, ADR-019, ADR-058.

### Future Revisions
- Two-tier content cache; embedding cache (SAD §36) reusing the content-cache pattern.

---

## ADR-019: Shared Explanation Cache

| Field | Value |
|---|---|
| **Status** | Accepted |
| **Date** | 2026-07-29 |
| **Related** | ADR-013, ADR-015, ADR-018, ADR-058 |

### Context
This ADR records the specific decision to cache Explanations and Translations at the **shared content layer** — the linchpin of both the cost flywheel (Product Strategy §9/§44) and the latency budget (CTO Constitution §25). The Domain Model (§13) makes this domain-model-correct, not just a performance hack: an `Explanation` is a Value Object defined entirely by its content — two identical explanations for the same sentence are interchangeable, which is *why* sharing them across learners is correct.

### Problem Statement
Each uncached Explanation/Translation costs an LLM call (the dominant variable cost). If these are generated per-learner, marginal AI cost never declines — the flywheel never spins, and unit economics stay flat-to-bad. We need a cache that reuses a generated explanation across every learner who encounters the same sentence, keyed so that it is correct (learner-independent), safe (no cross-language collisions), and invalidates on quality-affecting changes.

### Decision
**Cache Explanations and Translations in the shared Content Cache, keyed on `content_hash(sentence/word) + L1 + prompt_template_version + model_version`, sitting in front of the AI Gateway; a hit returns the cached Value Object ($0 marginal cost, no provider call); a miss generates, stores, then returns.**

- **Domain-correctness basis:** the `Explanation` VO's value-hash is the cache key (Domain Model §13). Sharing is correct because VOs of equal value are interchangeable.
- **Version components:** L1 (Bangla vs. future Hindi), prompt version (ADR-015), model version (ADR-014) — so a quality-affecting change invalidates correctly; nothing stale serves after a swap.
- **In front of the Gateway:** a hit never reaches a provider (ADR-013).
- **Long TTL:** Explanations are stable linguistic facts; invalidate on version/quality change only.
- **Never learner-keyed:** this is the cardinal separation (ADR-018).

### Alternatives Considered
1. **No shared cache (per-learner generation).** Rejected — destroys the cost flywheel; the business model's core lever (Product Strategy §44).
2. **Cache keyed on learner.** Rejected — no reuse; conflation bug (ADR-018).
3. **Cache keyed without version components.** Rejected — a prompt/model swap serves stale (possibly worse) explanations; a trust failure (CEO Vision §7).
4. **Short TTL (defensive).** Rejected — Explanations are stable; short TTL needlessly regenerates, wasting cost.

### Pros
- Near-zero marginal AI cost as the content library grows — the flywheel.
- Sub-3s latency met mostly by hits.
- Domain-correct (VO interchangeability).
- Quality-safe invalidation via versioning.

### Cons
- Version-key discipline mandatory.
- Cold-start cost (empty cache) until the library accumulates.

### Trade-offs
We accept version-discipline and cold-start cost in exchange for **the cost curve the business model depends on** — non-negotiable per Product Strategy §44.

### Consequences
- Cache-hit rate trends upward as content diversity stabilizes (the flywheel) — a tracked metric (ADR-058).
- Prompt/model swaps must bump versions (ADR-015/014).
- A concrete acceptable hit-rate threshold is *not* pre-decided (SAD §87) — set from real MVP data (ADR-058).

### Risks
- **Lower-than-modeled hit rate** (high content diversity) → cost curve degrades. Mitigated by monitoring from week one (ADR-058); not discovered at 10k users.
- **Stale content** (missed version bump). Mitigated by auto-derived versions.

### Migration Strategy
Greenfield. Cold-start mitigated by pre-seeding explanations for a curated seed content set (PRD §22).

### Related Documents
Domain Model §13; SAD §31, §87; Product Strategy §9, §44; CTO Constitution §26; SDD §13, §33; ADR-015, ADR-018, ADR-058.

### Future Revisions
- Embedding cache (SAD §36) reuses this pattern; two-tier cache at scale.

---

## ADR-020: Queue Architecture

| Field | Value |
|---|---|
| **Status** | Accepted |
| **Date** | 2026-07-29 |
| **Supersedes** | Legacy ADR-014 (broker half) — folded in |
| **Related** | ADR-009, ADR-012, ADR-016, ADR-032 |

### Context
The SAD (§23) requires that anything with unpredictable/non-trivial latency (transcription, ASR scoring, batch recalibration, notification dispatch) run asynchronously via a queue, never inline. The CTO Constitution (§27) demands idempotent, monitored, retry/dead-letter-handled jobs. The task stack includes Redis. The SDD (§34) defines separate queues per resource profile.

### Problem Statement
If unpredictable-latency work runs inline in requests, a slow transcription or ASR call ties up a web worker and blows the latency budget, degrading all users (CTO Constitution §34, 1k-user stage). If all jobs share one queue, a burst of heavy transcription starves lightweight notification jobs. We need a queue architecture with per-profile isolation, separate worker scaling, and reliable retry/dead-letter handling.

### Decision
**Adopt Redis-backed queues (Laravel Redis driver) with separate queues per resource profile, a separately-scaled worker pool, idempotent jobs, exponential backoff with capped retries, and a dead-letter queue with alerting.**

- **Redis as the queue transport** (ADR-012 Event Bus uses Redis too) — one broker at MVP, operational simplicity (CTO §3); swap to a dedicated broker at extraction is contained.
- **Per-profile queues (SDD §34):** `transcription`, `ai` (cache-miss generation), `pronunciation` (isolated, ADR-016), `notifications`, `default` — each scaled independently by depth.
- **Idempotent jobs:** every job is safe to retry (idempotency key, often the aggregate/event id); double-execution has no side effect (aligned with ADR-009 consumer idempotency).
- **Retry + backoff + DLQ:** exponential backoff, capped max attempts; exhausted jobs → dead-letter queue + alert (ADR-036). A silently-dropped job (a transcription stuck forever) is a correctness bug, not acceptable (SAD §23).
- **Workflows = aggregate state machines (SAD §24):** multi-step flows (Content Import) are the ContentSource aggregate's explicit state machine, each transition enqueuing the next job — queryable status, retry-from-failed-step recovery.

### Alternatives Considered
1. **Inline processing in requests.** Rejected — blows latency budgets; degrades all users (CTO §34).
2. **Single shared queue.** Rejected — heavy jobs starve light ones; no per-profile scaling.
3. **A dedicated broker (Kafka/SQS) from day one.** Rejected at MVP — operational complexity not yet earned (CTO §34); Redis suffices and is broker-agnostic via the Event Bus (ADR-012).
4. **Opaque job chains (no aggregate state machine).** Rejected — unqueryable status; recovery from failure intractable (SAD §24).

### Pros
- Bounded web-tier latency (heavy work off the request path).
- Per-profile independent scaling and isolation.
- Reliable (idempotent, retried, DLQ'd, alerted).
- Multi-step flows are queryable and recoverable.

### Cons
- Multiple worker pools to operate.
- Eventual consistency for async work (designed-for via status polling / future push).

### Trade-offs
We accept operational breadth (several pools) and eventual consistency in exchange for **bounded request latency, isolation, and reliability**.

### Consequences
- Queue depth + processing latency are first-class metrics (ADR-035).
- Pronunciation's isolated queue moves cleanly with extraction (ADR-046).
- The `ai` queue is throttled by the Gateway's cost-aware rate limiter (ADR-013).

### Risks
- **Non-idempotent job** double-executing. Mitigated by idempotency tests (ADR-030).
- **Silent DLQ growth** (jobs failing unnoticed). Mitigated by DLQ alerting (ADR-036).

### Migration Strategy
Greenfield. Dedicated broker at extraction (ADR-012/046) is contained.

### Related Documents
SAD §23, §24, §27, §53; CTO Constitution §27; SDD §34; ADR-009, ADR-012, ADR-016.

### Future Revisions
- Dedicated broker (SQS/Kafka) at the extraction stage.

---

## ADR-021: Database Selection

| Field | Value |
|---|---|
| **Status** | Accepted |
| **Date** | 2026-07-29 |
| **Supersedes** | Legacy ADR-006 (PostgreSQL) — renumbered |
| **Related** | ADR-007, ADR-009, ADR-010, ADR-045 |

### Context
The SAD (§72–§73) called for a single, proven relational database, logically partitioned by Bounded Context, selected via a CTO §3 ADR — deliberately not specified in the SAD. The task stack now mandates **PostgreSQL**. This ADR ratifies PostgreSQL as the selection, capturing the rationale the SAD deferred.

### Problem Statement
The system's data spans: transactional Core Domain state (Mastery, requiring strong consistency + optimistic locking), composite Value Objects (Explanations, well-served by JSONB), append-only streams (outbox, audit), and event-fed read projections. Polyglot persistence (a different store per concern) is operationally expensive and premature. We need one proven store that serves all these well at MVP scale, with a clear path to specialized stores later if genuinely needed.

### Decision
**Adopt PostgreSQL as the single primary relational database, logically partitioned by Bounded Context (each module owns its tables; no cross-module joins), with JSONB for composite Value Objects, optimistic-lock version columns on Core aggregates, and read replicas + sharding deferred to the SAD §53 scale stages.**

- **Selection rationale (CTO §3):** proven relational maturity; strong transactional guarantees for the Core Domain (SAD §75); JSONB for composite VOs (clean mapper round-trip, SDD §17); mature operational tooling/backups (CTO §33); deep team/ecosystem fluency; reversibility (standards-compliant).
- **Logical partitioning (SAD §72):** each module owns a disjoint table set; cross-module reference by identifier value only (ADR-005); no cross-module joins/FKs.
- **JSONB** for composite VOs not individually queried; real columns for query targets.
- **UUIDv7 primary keys** (time-ordered) for index locality (ADR-052 migration/index detail in SDD §32); bigint serials only for high-write append-only tables.
- **Specialized stores deferred:** vector DB (SAD §37) only if embeddings become load-bearing; search infra (ADR-022) only if a cross-user discovery feature is prioritized. Single-store simplicity now (YAGNI).

### Alternatives Considered
1. **MySQL.** Rejected — PostgreSQL's JSONB, partial indexes, and richer type/constraint system better serve the DDD/VO model and the selective-CQRS projections; comparable fluency, weaker fit.
2. **Polyglot persistence from day one** (e.g., DynamoDB for events, Redis-only for caches, Postgres for relational).** Rejected — operational complexity not yet earned (CTO §3); Postgres + Redis covers MVP cleanly.
3. **A NoSQL document store as primary.** Rejected — the Core Domain's relational/transactional needs (Mastery consistency, joins within a module) favor relational; document stores sacrifice the consistency guarantees the moat depends on.
4. **Event store as the primary write side (event sourcing).** Rejected at MVP — see ADR-007; the outbox preserves an event stream without committing to event sourcing.

### Pros
- One proven store, simpler to operate/backup/reason about.
- Strong consistency for the Core Domain; JSONB for VOs; partial indexes for hot queries.
- Clear path to read replicas (100k) and sharding (1M+) per SAD §53.
- Standards-compliant → reversible.

### Cons
- A single store means careful capacity planning at scale (mitigated by staged scaling, ADR-045).
- Specialized needs (vector search) require a future addition (deferred, not foreclosed).

### Trade-offs
We accept single-store capacity discipline in exchange for **operational simplicity and the consistency guarantees the Core Domain requires** — the CTO §3 "operational maturity over novelty" principle applied directly.

### Consequences
- One logical DB, module-owned tables; read replicas at 100k; sharding by `learner_id` at 1M+ (ADR-045).
- The outbox + audit tables live here (ADR-009).
- Migrations are module-namespaced (ADR-052).

### Risks
- **Single-store bottleneck at extreme scale** — mitigated by staged replicas/sharding (ADR-045).
- **Schema migration risk on the Core Domain** — mitigated by ADR-052 + expand/contract discipline.

### Migration Strategy
Greenfield. A future specialized store (vector DB) is an additive ADR, not a migration.

### Related Documents
SAD §72, §73, §87; CTO Constitution §3, §33; SDD §17, §30, §32; ADR-007, ADR-009, ADR-010, ADR-045, ADR-052.

### Future Revisions
- Read replicas (100k); sharding (1M+); vector DB if embeddings load-bearing.

---

## ADR-022: Search Engine

| Field | Value |
|---|---|
| **Status** | Accepted (Deferred Implementation) |
| **Date** | 2026-07-29 |
| **Related** | ADR-021, ADR-057 |

### Context
The SAD (§32) notes that a dedicated search infrastructure (Elasticsearch-class) is **not a core MVP requirement** — there is no full-text content library to search at launch (the marketplace/ecosystem vision is deferred, Product Strategy §31). When search is needed (a teacher searching assigned content, a learner searching their own imports), a straightforward indexed search over scoped data suffices.

### Problem Statement
Adopting a dedicated search engine (Elasticsearch/OpenSearch/managed search) prematurely adds operational complexity (a cluster to run, sync pipelines to maintain, eventual-consistency between primary store and search index) for a feature that doesn't exist yet. We need to decide *not* to build search infrastructure now, while preserving the ability to add it when a genuine cross-user discovery feature is prioritized.

### Decision
**Do not adopt a dedicated search engine at MVP. Use PostgreSQL's built-in full-text search (`tsvector`/`tsquery`, trigram indexes) for the scoped searches that exist (a learner's own imports, a teacher's assigned content). Defer dedicated search infrastructure (Elasticsearch/OpenSearch or a managed vector search) until a genuine cross-user content-discovery feature is prioritized (V3+ marketplace, Product Strategy §31), at which point an ADR supersedes this one.**

- **MVP search:** Postgres FTS over module-owned, scoped tables — sufficient for the actual use cases.
- **Deferred:** dedicated search infra, vector/semantic search (ties to ADR-021's vector-DB deferral, SAD §37).
- **YAGNI applied (CTO §2):** no search cluster to operate, sync, or back up until a real need exists.

### Alternatives Considered
1. **Elasticsearch/OpenSearch from day one.** Rejected — no cross-user discovery feature exists (Product Strategy §31 deferred); unearned operational complexity.
2. **A managed search service prematurely.** Rejected — same reasoning; cost + sync complexity for a non-need.
3. **No search at all.** Rejected — even MVP has legitimate scoped searches (own imports, assigned content); Postgres FTS serves these cheaply.

### Pros
- Zero search-cluster operational burden at MVP.
- Postgres FTS covers real scoped needs without new infra.
- The decision is reversible (add a search ADR when needed).

### Cons
- Postgres FTS has limits at scale for cross-user discovery (acceptable — that feature doesn't exist yet).
- A future migration to dedicated search requires a sync pipeline (deferred cost, accepted).

### Trade-offs
We accept a future migration cost (if/when discovery is built) in exchange for **no premature search infrastructure now** — YAGNI applied honestly.

### Consequences
- Search uses Postgres FTS + trigram indexes (SDD §32) for scoped queries.
- A dedicated-search ADR is a future candidate (final review).

### Risks
- **Postgres FTS insufficient when discovery is eventually built** — mitigated by the deferred dedicated-search ADR path (a known, planned migration, not a surprise).

### Migration Strategy
When cross-user discovery is prioritized: a new ADR selects managed search or vector search; a sync pipeline (outbox-fed, ADR-009) populates it.

### Related Documents
SAD §32, §37; Product Strategy §31; CTO Constitution §2; SDD §32; ADR-021.

### Future Revisions
- Dedicated/vector search ADR when discovery is prioritized (V3+).

---

## ADR-023: Storage Strategy

| Field | Value |
|---|---|
| **Status** | Accepted |
| **Date** | 2026-07-29 |
| **Supersedes** | Legacy ADR-004 (content-eligibility, copyright half) and legacy ADR-008 (S3 half) — folded in |
| **Related** | ADR-004, ADR-021, ADR-037, ADR-040, ADR-042 |

### Context
The SAD (§77–§79) defines object storage for media/transcripts with lifecycle-enforced retention, and a CDN for cached reusable excerpts. The PRD (§36) makes the copyright-mitigation stance explicit: LexiFlow **does not permanently re-host full copyrighted video/audio** — it stores transcripts, structured artifacts, and short excerpt references, relying on the source platform's embed for playback. The Domain Model (§15) sources the content-eligibility policy from this PRD analysis. The task stack mandates AWS (S3).

### Problem Statement
Storing media naively creates two failure modes: (1) copyright exposure from permanently re-hosting full copyrighted works (the PRD's biggest legal risk, PRD §36); (2) unbounded storage cost and retention liability (data the company no longer needs is a liability, CTO §32). We need a storage strategy that mitigates copyright risk, controls cost, and enforces retention automatically.

### Decision
**Adopt S3-compatible object storage (AWS S3) for media artifacts and transcripts, referenced by identifier from PostgreSQL (never as BLOBs), with: (a) the copyright-mitigation stance (store artifacts + short excerpts, not full copyrighted media; use source-platform embeds for playback); (b) automated lifecycle retention policies; (c) a CDN for cached reusable excerpts.**

- **Copyright mitigation (PRD §36/§79):** store transcripts, structured artifacts (Vocabulary Items, Explanations), and short excerpt references (for shadowing). Do **not** permanently store full copyrighted video/audio. For YouTube/content-platform sources, use the source's own embed/player.
- **Object storage (S3, ADR-040 for credentials):** media referenced by key from the `media_artifacts` table (Storage module); never BLOBs in Postgres (SAD §72).
- **Automated lifecycle (CTO §32):** pronunciation audio (short retention), user uploads (deleted on account deletion), transcripts/excerpts (retained while active then expired) — S3 lifecycle rules + a nightly purge job, not manual cleanup.
- **CDN (SAD §78):** cached reusable excerpts served via CDN — latency win for variable connectivity (Product Strategy §33) + DDoS offload (ADR-056).
- **Content-eligibility enforcement (legacy ADR-004, copyright half, ratified here):** the eligibility policy (from the PRD) is enforced as the first ContentSource transition (ADR-004), *before* any storage/processing cost.

### Alternatives Considered
1. **Permanently re-host full copyrighted media.** Rejected — the PRD's biggest legal risk (PRD §36); also cost-prohibitive.
2. **BLOBs in PostgreSQL.** Rejected — bloats the DB, harms backup/performance (SAD §72).
3. **Manual retention cleanup.** Rejected — forgetable; CTO §32 mandates automated deletion.
4. **Self-hosted object storage (MinIO in prod).** Rejected at MVP — operational burden; S3 (managed) satisfies CTO §3. MinIO retained for local dev/test (SDD §22).

### Pros
- Copyright risk controlled (artifacts + excerpts + embeds, not re-hosted media).
- Cost controlled (lifecycle expiry; CDN for reusable excerpts).
- Retention automated (compliance + hygiene).
- Operational simplicity (managed S3).

### Cons
- Embed-dependence on source platforms (soft underbelly: platform-policy risk, PRD §42) — monitored (ADR-036).
- Lifecycle rules need care to not delete needed data.

### Trade-offs
We accept platform-embed dependence in exchange for **copyright-risk control and cost control** — the embed stance is the mitigation the PRD mandates.

### Consequences
- The Storage module is the sole S3 access point; lifecycle enforced at its boundary.
- Media references are by key, not embedded.
- Platform-policy changes (YouTube ToS) are a monitored risk (ADR-036).

### Risks
- **Platform embed/API terms change** (PRD §42) — mitigated by the adapter seam (ADR-005) + monitoring; not a single-point-of-failure for the whole business if multiple source types are supported over time.
- **Retention rule over-deletes.** Mitigated by conservative rules + review.

### Migration Strategy
Greenfield. Multi-region S3 replication at the 1M+ stage (ADR-045).

### Related Documents
PRD §36, §42; SAD §63, §77, §78, §79; CTO Constitution §32; Domain Model §15, §18; SDD §37; ADR-004, ADR-040, ADR-042.

### Future Revisions
- Multi-region replication; additional source-platform adapters as sources diversify.

---

## ADR-024: Authentication

| Field | Value |
|---|---|
| **Status** | Accepted |
| **Date** | 2026-07-29 |
| **Related** | ADR-025, ADR-026, ADR-040, ADR-056 |

### Context
The CTO Constitution (§29) mandates standard, industry-proven OAuth/OIDC + email auth — **no home-grown authentication logic, ever.** The SAD (§44) fixes the token model: short-lived access tokens + refresh-token rotation, validated by the API Gateway on every request, with Identity as the sole source of truth for "who is this." The task stack runs Laravel 12.

### Problem Statement
Home-grown auth (custom password hashing, token signing, session logic) is the single most error-prone and security-critical category to get subtly wrong, with well-documented catastrophic failure modes. The target market's mobile-first, variable-connectivity reality (Product Strategy §33) also demands low-friction auth (social OAuth) and retry-safe token handling. We need proven, library-based auth with a sound token model.

### Decision
**Adopt library-based authentication (Laravel's auth scaffolding + Sanctum for SPA/API tokens, or a proven OAuth/OIDC package), email + password with verification and social OAuth (Google + Bangladesh-relevant providers), short-lived access tokens with refresh-token rotation, and first-class account-deletion/data-export flows (privacy rights). No home-grown auth crypto.**

- **Library-based (CTO §29):** password hashing via PHP `password_hash` (argon2id); token issuance/refresh/revocation library-handled.
- **Flows:** email+password (verification), social OAuth (low-friction, mobile-first), password reset (signed short-lived links), account deletion + data export (privacy rights, CTO §32).
- **Token model (SAD §44):** short-lived access tokens + refresh-token rotation (rotate on use; detect reuse → revoke).
- **Identity as sole source of truth:** no module duplicates identity state; other modules reference `LearnerId`.
- **Minors tagging:** under-18 accounts (school features) are tagged for elevated downstream handling (ADR-041).

### Alternatives Considered
1. **Home-grown auth.** Rejected — CTO §29 explicitly forbids; zero benefit, real catastrophic risk.
2. **Long-lived tokens / no rotation.** Rejected — current best practice is short-lived + rotation; long-lived tokens amplify breach impact.
3. **Storing access tokens in localStorage without XSS mitigation.** Rejected — httpOnly cookies preferred for the SPA to reduce XSS token theft.
4. **No social OAuth.** Rejected — friction cost in a mobile-first, conversion-sensitive market (Product Strategy §33).

### Pros
- Avoids the catastrophic risk class of DIY auth.
- Low-friction (social) for the target market.
- Sound token model (short-lived + rotation).
- Privacy-rights flows first-class.

### Cons
- Dependency on the auth package's quality (audited per ADR-049).

### Trade-offs
We accept dependency on a proven package in exchange for **avoiding the most dangerous category of DIY error** — the correct trade for security-critical infrastructure.

### Consequences
- Identity is a thin Generic module wrapping the package; its events (`LearnerRegistered`) are the integration points.
- Token handling is standardized across web + future external API.

### Risks
- **Package vulnerability** — mitigated by ADR-049 dependency auditing.
- **Token theft via XSS** — mitigated by httpOnly cookies + CSP.

### Migration Strategy
Greenfield. Enterprise SSO (SAML/OIDC, Year 4) extends Identity, not rewrites it.

### Related Documents
CTO Constitution §29; SAD §44; SDD §23; ADR-025, ADR-026, ADR-040, ADR-049, ADR-056.

### Future Revisions
- Enterprise SSO (Year 4); passkeys/WebAuthn as they mature.

---

## ADR-025: Authorization

| Field | Value |
|---|---|
| **Status** | Accepted |
| **Date** | 2026-07-29 |
| **Related** | ADR-024, ADR-026, ADR-041, ADR-056 |

### Context
The CTO Constitution (§30) mandates RBAC enforced at the service/Application layer on every request, **never trusted from client-supplied state.** The SAD (§45) requires defense in depth: authorization checked at every handler, never relying on a Gateway-level "already authorized" flag. The privacy invariants (absolute Mastery isolation; aggregated-only classroom visibility — Domain Model §16) are authorization decisions enforced structurally.

### Problem Statement
If authorization is checked only at the Gateway (a single point), a bypass or misconfiguration exposes data. If client-supplied role/id is trusted, an attacker simply sends a different `learner_id`. If per-item Mastery leaks to the Classroom module, the privacy invariant breaks. We need defense-in-depth authorization that is enforced at every use case and that structurally enforces the privacy invariants.

### Decision
**Adopt defense-in-depth authorization: RBAC (ADR-026) enforced at every command/query handler as its first step (never trusting a Gateway flag or client-supplied state), with the privacy invariants (absolute Mastery isolation, aggregated-only classroom visibility) enforced at the query-interface level.**

- **At every handler (SAD §45):** each use case invokes an Application-layer Policy as step 1; Laravel Policies implement per-resource rules.
- **Never trust client state (CTO §30):** every query scoped to the authenticated principal; a client-supplied `learner_id` is verified for ownership/roster-membership.
- **Privacy invariants structural:**
  - *Absolute Mastery isolation:* no "fetch another learner's mastery" code path exists; queries are self-scoped.
  - *Aggregated-only classroom (Domain Model §6/§16):* the only Classroom-permitted query returns aggregates; per-item Mastery never crosses the boundary — enforced at the LearnerModel query interface, not just UI.
- **Tier-gating (Domain Model §15):** enforced at each consuming module's command boundary (never assumed pre-checked); `TierGateException` (402) is the honest paywall.
- **Audit (CTO §20):** authz-sensitive actions write to the separate audit log.

### Alternatives Considered
1. **Gateway-only authorization.** Rejected — single point of failure; CTO §28 defense in depth forbids trusting it as sufficient.
2. **Trusting client-supplied role/id.** Rejected — trivially exploitable.
3. **Enforcing aggregated-visibility only in the UI.** Rejected — a different client or a bug re-leaks per-item data; the invariant must be structural (SAD §51).
4. **Assuming tier is pre-checked at consumers.** Rejected — bypass risk; enforced at each boundary.

### Pros
- No single point of authorization failure.
- Privacy invariants structurally unbreakable.
- Honest paywall (402) aids conversion UX.

### Cons
- Discipline across every handler (mitigated by review gate ADR-055 + a best-effort static check for missing authz).

### Trade-offs
We accept per-handler authorization discipline in exchange for **no single point of failure and structurally-enforced privacy** — the correct trade for a system handling minors' data.

### Consequences
- A handler without an explicit Policy check fails review (ADR-055).
- The Classroom→LearnerModel aggregated query is the single choke point, heavily tested (ADR-030).

### Risks
- **A handler missing its authz check** — mitigated by review + static check + audit-log anomalies.
- **Aggregated-query regression** leaking detail — mitigated by the choke-point test.

### Migration Strategy
Greenfield. ABAC (attribute-based) for fine-grained enterprise policies (Year 4) extends RBAC.

### Related Documents
CTO Constitution §28, §30; SAD §45, §51, §63; Domain Model §6, §16; SDD §24; ADR-024, ADR-026, ADR-041.

### Future Revisions
- ABAC for enterprise (Year 4); policy-as-code evaluation at scale.

---

## ADR-026: RBAC

| Field | Value |
|---|---|
| **Status** | Accepted |
| **Date** | 2026-07-29 |
| **Related** | ADR-024, ADR-025, ADR-045 |

### Context
The CTO Constitution (§30) and SAD (§45) define the four roles matching the PRD's feature set: **learner, teacher, school admin, platform admin.** RBAC is the chosen model (not ABAC) at MVP for simplicity.

### Problem Statement
Without an explicit role model, authorization devolves into ad-hoc flags scattered across handlers — unmaintainable, inconsistent, and error-prone. We need a small, explicit, role-based model that matches the product's actual personas and is enforceable.

### Decision
**Adopt RBAC with four roles — `learner`, `teacher`, `school_admin`, `platform_admin` — assigned to identities, traveling in token claims, enforced via Laravel Policies at each handler (ADR-025). Keep the role set minimal; resist adding roles prematurely.**

- **Four roles** (PRD/CTO §30): learner (self-scoped data), teacher (their classrooms' aggregated data), school_admin (institution-wide aggregated data), platform_admin (operational/admin).
- **Assigned to identities; in token claims** (Identity module, ADR-024).
- **Enforced via Policies** at every handler (ADR-025), never client-trusted.
- **Minimal role set (YAGNI):** no "premium_learner" or "moderator" roles; tier-gating (ADR-025) handles premium differences via subscription state, not a role.

### Alternatives Considered
1. **ABAC from day one.** Rejected — over-engineering for MVP's four clear roles (CTO §2 YAGNI); ABAC is a Year-4 enterprise extension.
2. **Ad-hoc permission flags per handler.** Rejected — unmaintainable, inconsistent.
3. **More granular roles** (e.g., per-feature roles).** Rejected — role explosion; tier-gating + Policies handle nuance.

### Pros
- Simple, explicit, matches the personas.
- Enforceable via Policies; easy to audit.
- Extensible to ABAC later without replacing.

### Cons
- RBAC's coarseness may need ABAC for fine-grained enterprise needs (deferred).

### Trade-offs
We accept RBAC's coarseness at MVP in exchange for **simplicity and auditability** — ABAC complexity is deferred to when enterprise needs justify it.

### Consequences
- Four roles; Policies per resource; tier differences via subscription state.

### Risks
- **Role-permission drift** as features grow — mitigated by a central role-permission matrix (reviewed in ADR-054).

### Migration Strategy
Greenfield. ABAC extension (Year 4) layers atop RBAC.

### Related Documents
CTO Constitution §30; SAD §45; SDD §24; ADR-024, ADR-025.

### Future Revisions
- ABAC for enterprise institutional policies (Year 4).

---

## ADR-027: API Versioning

| Field | Value |
|---|---|
| **Status** | Accepted |
| **Date** | 2026-07-29 |
| **Related** | ADR-028, ADR-029, ADR-051 |

### Context
The SAD (§18) requires resource-oriented, versioned-from-the-URL-path (`/v1/...`) APIs. The CTO Constitution (§15) mandates SemVer for external-consumer surfaces and explicit internal-contract versioning. The public client-facing API (Year 3–4 external consumers, Product Strategy §36) is distinct from internal module contracts.

### Problem Statement
Without explicit versioning, a breaking change silently breaks clients (the Vue app, future partners) with no rollback path. Without distinguishing public vs. internal contracts, internal evolution freedom is constrained by accidental external coupling. We need a versioning strategy that makes breaking changes deliberate and visible.

### Decision
**Adopt URL-path versioning for the public API (`/v1/...`), SemVer once external consumers exist, additive changes non-breaking and breaking changes deliberate/reviewed/visible; internal module contracts versioned explicitly (via the `*Module.php` declarations and event versioning, ADR-051) so a breaking internal change is a reviewed event across consumers.**

- **Public API:** `/v1/...` in the path; SemVer for the public surface once external consumers exist (Year 3–4).
- **Additive = non-breaking; breaking = deliberate, reviewed, cross-consumer.**
- **Internal contracts:** versioned via module declarations + event shapes (ADR-051); a breaking internal change is a reviewed, documented event, never silent.
- **Public ≠ internal (SAD §17):** the public API composes module Application layers but is never a passthrough to internal Domain objects — preserving module evolution freedom.

### Alternatives Considered
1. **No versioning (always-latest).** Rejected — silent client breakage; no rollback.
2. **Header-based versioning.** Rejected — less visible/cachable than URL-path; harder for partners.
3. **Single internal+public contract.** Rejected — couples internal evolution to external stability.

### Pros
- Breaking changes are deliberate and visible.
- Internal evolution freedom preserved.
- Partner-friendly (URL-path, SemVer).

### Cons
- Version maintenance overhead once v2 exists (deferred until external consumers).

### Trade-offs
We accept version-maintenance overhead (later) in exchange for **no silent breakage and evolution freedom**.

### Consequences
- `/v1/...` routes; internal contracts versioned via declarations/events.
- A breaking change triggers the review gate (ADR-054).

### Risks
- **Accidental breaking change** — mitigated by contract tests (ADR-029/030) + review.

### Migration Strategy
Greenfield. v2 introduced deliberately when needed.

### Related Documents
SAD §17, §18, §70; CTO Constitution §15; SDD §25, §26; ADR-028, ADR-029, ADR-051.

### Future Revisions
- v2 when a breaking public change is justified.

---

## ADR-028: REST vs GraphQL

| Field | Value |
|---|---|
| **Status** | Accepted |
| **Date** | 2026-07-29 |
| **Related** | ADR-027, ADR-029 |

### Context
The SAD (§19) does **not** adopt GraphQL at MVP — REST is simpler to secure, cache, and reason about for a small team, and the client surface (a single web/mobile app) doesn't yet have the query-shape diversity that justifies GraphQL's complexity. Revisit only when the API opens to heterogeneous external consumers (Product Strategy §36, Year 3–4).

### Problem Statement
GraphQL offers client-driven query flexibility but adds complexity (a schema, resolvers, N+1 risk, harder caching/securing, a steeper security model for a small team). Adopting it prematurely for a single client with predictable needs is unearned complexity. We must decide the API style for MVP and when (if ever) to revisit.

### Decision
**Adopt REST for the MVP API (resource-oriented, ADR-027), with OpenAPI as the contract (ADR-029). Do not adopt GraphQL at MVP. Revisit GraphQL only if/when the API opens to diverse external consumers whose query-shape needs justify its complexity (Year 3–4 API strategy, Product Strategy §36).**

- **MVP:** REST + OpenAPI; sufficient for the single Vue/Inertia client.
- **Deferred:** GraphQL (revisit at API-licensing stage).
- **YAGNI applied:** no GraphQL schema/resolvers to build, secure, or optimize prematurely.

### Alternatives Considered
1. **GraphQL from day one.** Rejected — unearned complexity for a single client; harder to secure/cache (SAD §19).
2. **gRPC for internal + REST for external.** Rejected at MVP — internal calls are in-process (ADR-008); gRPC adds infrastructure now for no benefit.
3. **A hybrid (REST + a small GraphQL read surface).** Rejected — premature; the read surface is served by REST + selective CQRS (ADR-007).

### Pros
- Simpler to secure, cache, and reason about.
- Lower team expertise barrier.
- Sufficient for the single client.
- Reversible (add GraphQL later if justified).

### Cons
- Less flexible for future diverse clients (acceptable — they don't exist yet).

### Trade-offs
We accept less future-client flexibility now in exchange for **simplicity and security** — revisit when flexibility pays for its complexity.

### Consequences
- REST + OpenAPI is the API style; GraphQL is a documented future candidate.

### Risks
- **Re-evaluating too late** when diverse clients arrive — mitigated by the Year 3–4 trigger.

### Migration Strategy
Greenfield. A GraphQL ADR is a future candidate if external consumers justify it.

### Related Documents
SAD §19; Product Strategy §36; CTO Constitution §2; ADR-027, ADR-029.

### Future Revisions
- GraphQL evaluation at the API-licensing stage (Year 3–4).

---

## ADR-029: OpenAPI First

| Field | Value |
|---|---|
| **Status** | Accepted |
| **Date** | 2026-07-29 |
| **Related** | ADR-027, ADR-028, ADR-030, ADR-047 |

### Context
The CTO Constitution (§2) adopts **API First**: every service's contract is designed and reviewed before implementation, enabling parallel human + AI-agent work without collision. The SDD (§25) fixes OpenAPI 3.1 as the single source of truth, with TS types generated for the Vue client.

### Problem Statement
If the API contract is an afterthought (documented after implementation, or maintained separately from code), it drifts from reality, the client and server disagree, and AI agents can't work in parallel safely. We need the contract to be the source, not a derivative.

### Decision
**Adopt OpenAPI 3.1 as the single source of truth for the public API contract: design/review the spec first; generate TS types for the Vue client from it (never hand-write); implement server + client against it; contract-test that the server matches the spec.**

- **Spec-first workflow (SDD §25):** design → peer-review → generate types → implement → contract-test.
- **Generated TS types only** (no hand-written server-contract types) — drift impossible.
- **Contract tests** verify server↔spec conformance (ADR-030).
- **Internal contracts** are the module declarations + events (ADR-027/051), not OpenAPI.

### Alternatives Considered
1. **Code-first (generate spec from code).** Rejected — the spec becomes a derivative, not a review artifact; loses the design-first discipline.
2. **Hand-written TS types.** Rejected — guaranteed drift.
3. **No formal contract.** Rejected — collision-prone parallel work; AI agents can't reason safely.

### Pros
- Contract drift impossible (codegen).
- Parallel human + AI work without collision.
- Design review happens before implementation cost.
- Partner-SDK generation ready for the future API.

### Cons
- A codegen step in the build (low cost).
- Spec authoring discipline required.

### Trade-offs
We accept a codegen step and spec-authoring discipline in exchange for **zero contract drift and safe parallel work**.

### Consequences
- The OpenAPI spec in `openapi/` is authoritative; a generated-types diff without a spec change fails CI.
- Contract tests are part of the suite (ADR-030).

### Risks
- **Spec/code divergence** — mitigated by contract tests + CI diff gate.

### Migration Strategy
Greenfield. Partner SDKs generated from the same spec later.

### Related Documents
CTO Constitution §2; SAD §17; SDD §8, §25; ADR-027, ADR-028, ADR-030.

### Future Revisions
- Partner SDK generation at the API-licensing stage.

---

## ADR-030: Testing Pyramid

| Field | Value |
|---|---|
| **Status** | Accepted |
| **Date** | 2026-07-29 |
| **Related** | ADR-001, ADR-009, ADR-015, ADR-049, ADR-055 |

### Context
The CTO Constitution (§38) sets the testing philosophy: tests make change safe, not hit a coverage number; the Core Domain gets the deepest investment including property-based tests; every service boundary has integration coverage; every bug fix includes a regression test. §39 adds Bangla-accuracy QA as a distinct discipline. The SDD (§49) fixes the tailored pyramid.

### Problem Statement
A vanity coverage number hides untested risky paths. Uniform shallow testing leaves the moat (Core Domain) under-protected and the boundaries (where AI-assisted dev breaks things silently) untested. Without a regression rule, bugs recur. Without a Bangla-quality gate, trust-critical regressions slip. We need a testing strategy that is risk-weighted, not metric-weighted.

### Decision
**Adopt a risk-weighted test pyramid: deepest investment in the Core Domain (property-based tests for the Interval Calculator and the Mastery-update impossibility of direct-set), integration coverage at every boundary (incl. outbox + idempotent consumers), contract tests (OpenAPI conformance), architecture tests (dependency rules), feature/E2E for key journeys, and a distinct Bangla-accuracy QA discipline (native-speaker eval set). Every bug fix carries a regression test. Coverage is tracked on Core Domain logic specifically, not as a global vanity metric.**

- **Layers (SDD §49):** Unit (Domain, pure) → Application (handlers) → Integration (module+DB+queue+cache) → Contract (OpenAPI/provider) → Architecture (dependency rules) → Feature/E2E → Bangla-accuracy QA.
- **Core Domain elevated (ADR-001):** property-based tests (determinism, monotonicity, boundaries); the "no direct-set Mastery" impossibility test.
- **Boundary integration (CTO §38):** outbox relay, consumer idempotency, mapper round-trip.
- **Regression rule:** every bug fix includes a regression test in the same change.
- **Bangla QA (CTO §39):** eval set gating prompt/provider changes (ADR-015).
- **No vanity coverage:** tracked on Core Domain logic specifically.

### Alternatives Considered
1. **Uniform coverage target across the codebase.** Rejected — CTO §38; hides risky paths; doesn't protect the moat.
2. **No property-based tests.** Rejected — the Core Domain's edge cases need them; deterministic tests miss classes of bugs.
3. **Reactive QA only.** Rejected — Bangla quality is trust-critical; needs a proactive eval gate (ADR-015).
4. **Skipping boundary tests** "because units pass."** Rejected — boundaries are where AI-assisted dev breaks silently (CTO §38).

### Pros
- Protects the moat (Core Domain) most rigorously.
- Catches boundary/contract breaks in CI.
- Prevents regressions (regression rule).
- Guards the trust promise (Bangla eval gate).

### Cons
- Higher test-investment cost on Core Domain (the right cost).

### Trade-offs
We accept unequal test investment (most on Core Domain) in exchange for **protecting the highest-value, highest-risk code** — aligned with ADR-001.

### Consequences
- CI runs the full pyramid; architecture tests run on every push (fast).
- Prompt/provider changes gated on the Bangla eval set.
- Flaky tests are defects (fix or quarantine immediately).

### Risks
- **Eval-set coverage gaps** — mitigated by growing it from real corrections.
- **Flaky tests eroding trust** — mitigated by the fix-or-quarantine rule.

### Migration Strategy
Greenfield. Automated LLM-as-judge Bangla evals as volume grows.

### Related Documents
CTO Constitution §38, §39, §40; SAD §38; SDD §49; ADR-001, ADR-009, ADR-015, ADR-055.

### Future Revisions
- Automated Bangla eval; event-sourced test fixtures at scale.

---

## ADR-031: CI/CD

| Field | Value |
|---|---|
| **Status** | Accepted |
| **Date** | 2026-07-29 |
| **Related** | ADR-030, ADR-032, ADR-038, ADR-049 |

### Context
The CTO Constitution (§14/§16) mandates trunk-based development, continuous deployment to staging on every merge, gated/auditable/rollback-capable production deploys, and feature flags decoupling deployment from release. The SAD (§66) and SDD (§48) fix the pipeline.

### Problem Statement
Without CI/CD, integration is painful, deploys are risky and manual, rollback is ad-hoc, and the team reverts to long-lived branches (where architectural drift and painful merges originate, CTO §14). Without automated quality gates, defects reach production. We need a pipeline that makes small, frequent, safe deploys the path of least resistance.

### Decision
**Adopt trunk-based development with short-lived branches; CI runs the full automated suite (ADR-030) on every push and merge; every merge to `main` auto-deploys to staging; production deploys are gated, auditable, instantly-rollback-capable actions; feature flags (ADR-038) decouple deployment from release. Build-once/config-differ images promote Testing → Staging → Production.**

- **Trunk-based (CTO §14):** `feature/`/`fix/`/`chore/` branches, merged within days; long branches forbidden; unfinished work ships behind flags.
- **CI (SAD §66):** Pint, PHPStan, ESLint, architecture tests (fast, every push) + integration/E2E (on PR); fail-fast parallel stages.
- **Staging auto-deploy** on merge; **production** gated + auditable + instant-rollback until traffic/maturity justify fully-auto prod deploys.
- **Feature flags (ADR-038):** deployment ≠ release; ship dark, turn on independently.
- **Build once, promote (SDD §48):** same image across environments, differing by config/secrets (ADR-039/040).

### Alternatives Considered
1. **GitFlow / long-lived branches.** Rejected — drift, painful merges (CTO §14).
2. **Manual deploys.** Rejected — risky, slow, non-auditable.
3. **Fully-auto production deploys from day one.** Rejected — until traffic/team maturity justify it (CTO §16).
4. **Environment-specific builds.** Rejected — parity loss; build-once is superior.

### Pros
- Small, frequent, safe deploys; fast feedback.
- Instant rollback; auditable history.
- Deployment/release decoupled (safe dark launches).

### Cons
- CI/CD investment upfront (the right cost).
- Flag hygiene discipline (ADR-038).

### Trade-offs
We accept pipeline investment and flag discipline in exchange for **safe, frequent deploys and low release risk**.

### Consequences
- The suite must stay fast and reliable (architecture tests every push).
- Rollback is a first-class operation (prior image + migration reversal, ADR-052).

### Risks
- **Slow/flaky CI** eroding trunk-based flow — mitigated by the fast architecture-tests split + flaky-test rule (ADR-030).
- **Flag sprawl** — mitigated by ADR-038 hygiene.

### Migration Strategy
Greenfield.

### Related Documents
CTO Constitution §14, §16; SAD §66; SDD §48; ADR-030, ADR-032, ADR-038.

### Future Revisions
- Fully-auto production deploys once DORA metrics justify.

---

## ADR-032: Deployment Strategy

| Field | Value |
|---|---|
| **Status** | Accepted |
| **Date** | 2026-07-29 |
| **Supersedes** | Legacy ADR-010 (Terraform + ECS/EKS) — renumbered |
| **Related** | ADR-021, ADR-031, ADR-038, ADR-040, ADR-044, ADR-045 |

### Context
The SAD (§11, §65) mandates containerized deployment via orchestration, single-region/multi-AZ at launch (CTO §36), IaC for all infrastructure. The task stack mandates Docker + AWS. This ADR ratifies the deployment topology and the IaC/orchestration choices (legacy ADR-010).

### Problem Statement
Hand-provisioned infrastructure is non-reproducible, hard to recover (ADR-044), and blocks the staged scaling plan (ADR-045). We need a declarative, version-controlled, repeatable deployment model on AWS that supports stateless horizontal scaling, a separately-scaled worker pool, and multi-AZ resilience.

### Decision
**Adopt containerized deployment (Docker) on AWS orchestration (ECS Fargate preferred for operational simplicity; EKS if team/container needs grow), all infrastructure defined in Terraform (IaC), single-region/multi-AZ at launch, stateless app instances behind a load balancer, separately-scaled worker pools (ADR-020), with managed Postgres (RDS), Redis (ElastiCache), and S3.**

- **Orchestration (CTO §3):** ECS Fargate (serverless containers, lowest ops burden) preferred; EKS only if Kubernetes-specific needs emerge. Evaluated against team fluency + operational maturity.
- **IaC = Terraform** (provider-agnostic, mature, reversible) — all compute/DB/cache/storage/network/secrets declarative + version-controlled.
- **Topology (SAD §11):** single-region, multi-AZ; stateless app tier; separate worker pools; managed stateful services (RDS with failover, ElastiCache).
- **Multi-region deferred** to the 1M+ stage (ADR-045) where real latency/availability needs justify it (CTO §36).

### Alternatives Considered
1. **Hand-provisioned infrastructure.** Rejected — non-reproducible, unrecoverable cleanly (ADR-044), blocks scaling.
2. **EKS (Kubernetes) from day one.** Rejected as default — operational burden not yet earned (CTO §3); Fargate is simpler. EKS remains an option if needs grow.
3. **Non-Terraform IaC (CloudFormation-only).** Rejected — Terraform's provider-agnosticism + ecosystem satisfy CTO §3 better; reversibility.
4. **Multi-region at launch.** Rejected — CTO §36; unearned complexity/cost.

### Pros
- Reproducible, recoverable, repeatable environments (ADR-044).
- Operational simplicity (managed services + Fargate).
- Clean scaling path (ADR-045).

### Cons
- AWS-specific knowledge; Terraform state management discipline.

### Trade-offs
We accept provider-specificity (AWS) and IaC discipline in exchange for **reproducibility, recovery, and operational simplicity** — AWS satisfies CTO §3 (team fluency, managed-service maturity).

### Consequences
- All infra is Terraform; environments differ by variables (ADR-039).
- Stateless tier scales horizontally; workers scale by queue depth.

### Risks
- **Terraform state drift** — mitigated by remote state + CI plan/apply discipline.
- **Vendor lock-in** — mitigated by IaC portability + standards-based stores (Postgres/Redis/S3 are widely portable).

### Migration Strategy
Greenfield. Multi-region is a staged ADR-045 addition.

### Related Documents
SAD §11, §36, §53, §65; CTO Constitution §3, §34, §36; SDD §48; ADR-021, ADR-031, ADR-040, ADR-044, ADR-045.

### Future Revisions
- Multi-region (1M+); EKS if Kubernetes needs emerge.

---

## ADR-033: Observability

| Field | Value |
|---|---|
| **Status** | Accepted |
| **Date** | 2026-07-29 |
| **Related** | ADR-034, ADR-035, ADR-036, ADR-058 |

### Context
The CTO Constitution (§23) requires observability **from day one**, not retrofitted after an incident. The SAD (§58–§61) fixes structured logs, metrics, and traces correlated by a shared request/event identifier — even within the monolith, so extraction doesn't change the tracing.

### Problem Statement
Monitoring tells you *that* something is wrong; observability lets you determine *why* without reproducing locally (CTO §23). Retrofitting observability after an incident is reliably more expensive and less complete. We need logs, metrics, and traces correlated end-to-end from the first commit.

### Decision
**Adopt the three-pillar observability model — structured logs (ADR-034), metrics (ADR-035), distributed traces (ADR-036) — all correlated by a shared `request_id`/`event_id`, instrumented at module boundaries from day one, even within the monolith (so extraction doesn't change tracing).**

- **Three pillars:** logs (what happened, with context), metrics (numeric health/behavior), traces (cross-boundary causality).
- **Correlation (SAD §61):** a middleware-assigned `request_id` propagates through events (carrying `event_id`); the same identifier joins logs/metrics/traces.
- **In-process tracing now** so a Content Import → Linguistic Analysis → Learner Model flow is followable end-to-end — and survives becoming network calls at extraction.
- **Per-module instrumentation (CTO §22):** latency, errors, domain metrics at boundaries.

### Alternatives Considered
1. **Monitoring without observability (alerts only).** Rejected — tells *that*, not *why*; CTO §23.
2. **Retrofitting after incidents.** Rejected — more expensive, less complete (CTO §23).
3. **Uncorrelated logs/metrics/traces.** Rejected — cross-boundary debugging impossible.

### Pros
- Determine *why* without local reproduction.
- Extraction-safe tracing (same identifier across network hops later).
- Per-module blast-radius visibility.

### Cons
- Instrumentation discipline + tooling cost from day one (the right cost).

### Trade-offs
We accept day-one instrumentation cost in exchange for **debuggability and extraction-safe observability**.

### Consequences
- Every module exposes logs/metrics/traces at boundaries; CI-blocking for production-readiness.
- Correlation IDs are mandatory in logs/events.

### Risks
- **Instrumentation gaps** — mitigated by a production-readiness checklist (per-module baseline metrics required).

### Migration Strategy
Greenfield. Multi-region trace aggregation at scale (ADR-045).

### Related Documents
CTO Constitution §22, §23; SAD §58–§61; SDD §20, §47; ADR-034, ADR-035, ADR-036.

### Future Revisions
- Multi-region observability aggregation (1M+).

---

## ADR-034: Logging

| Field | Value |
|---|---|
| **Status** | Accepted |
| **Date** | 2026-07-29 |
| **Related** | ADR-033, ADR-041, ADR-056 |

### Context
The CTO Constitution (§21) mandates structured logging with PII/secrets **never logged in plaintext** — a hard rule given minors' and payment data. The SAD (§59) requires redaction **enforced at the logging infrastructure layer**, not left to call-site discipline. The SDD (§20) fixes the `RedactingLogger`.

### Problem Statement
String-concatenated free-text logs are unparseable and routinely leak PII/secrets when engineers interpolate (`"user $email did $x"`). Given minors' data and payment data flow through the system, a logging discipline left to individual engineers is a security control failure waiting to happen. We need structured logging with architectural redaction.

### Decision
**Adopt structured JSON logging with a fixed schema (timestamp, level, message, request_id, module, event_type, context), a small enforced severity set, and a `RedactingLogger` wrapper that strips known-sensitive fields (secrets, payment data, raw audio, full transcripts, PII beyond correlation needs) before emission — redaction enforced at the infrastructure layer, not by call-site discipline. Audit logs are a separate stream with stricter retention/access.**

- **Structured only** (CTO §21): JSON, fixed schema; never free-text concatenation.
- **Severity set** (`debug`/`info`/`warn`/`error`/`fatal`) defined centrally.
- **Architectural redaction (SAD §59):** the `RedactingLogger` redacts by key name + pattern (emails hashed, tokens/audio/full-transcripts stripped) — impossible to accidentally log a secret.
- **Correlation:** every entry carries `request_id` (+ `event_id`).
- **Separate audit log** (CTO §20): authz-sensitive actions, stricter retention/access.

### Alternatives Considered
1. **Free-text logs.** Rejected — unparseable; PII leaks.
2. **Call-site redaction discipline.** Rejected — forgetable; one missed call leaks PII.
3. **Single mixed log (ops + audit).** Rejected — audit needs stricter controls (CTO §20).

### Pros
- Privacy invariant unbreakable by accident (architectural redaction).
- Parseable, correlated, debuggable.
- Clean audit separation.

### Cons
- Redaction rule set maintenance (new sensitive fields must be added in the same PR that introduces them).

### Trade-offs
We accept redaction-rule maintenance in exchange for **unbreakable privacy in logs** — non-negotiable for minors'/payment data.

### Consequences
- A new sensitive field type requires a redaction-rule update in the same PR (review-blocking).
- The `RedactingLogger` is the only logger Domain/Application resolve; direct `Log::` facade use outside Infrastructure is linted.

### Risks
- **A new PII field not added to redaction rules.** Mitigated by the same-PR rule + review.

### Migration Strategy
Greenfield.

### Related Documents
CTO Constitution §21; SAD §59; SDD §20; ADR-033, ADR-041, ADR-056.

### Future Revisions
- Log-volume sampling/cost controls at scale.

---

## ADR-035: Metrics

| Field | Value |
|---|---|
| **Status** | Accepted |
| **Date** | 2026-07-29 |
| **Related** | ADR-033, ADR-036, ADR-058 |

### Context
The CTO Constitution (§22) requires every service expose uptime, latency (p50/p95/p99), and error-rate as a baseline before production-readiness. The SAD (§60) adds the AI-cost dashboard treated with availability-incident severity.

### Problem Statement
Without a baseline metric set, a module is "production-ready" by assertion, not evidence. Without cost metrics, the dominant scaling risk (AI cost) is invisible operationally. We need a consistent baseline + cost-as-first-class metrics.

### Decision
**Adopt a baseline metric set for every module (uptime, latency p50/p95/p99, error rate) plus domain-relevant metrics (cache hit rate, queue depth, provider-call rate, cost per request), with AI-cost metrics treated as first-class operational health (ADR-058). Metrics are correlated by the same identifiers as logs/traces (ADR-033).**

- **Baseline (CTO §22):** uptime, latency percentiles, error rate — required before production-readiness.
- **Domain metrics:** cache hit/miss, queue depth/latency, provider calls, per-tier request distribution.
- **Cost-as-first-class (SAD §60):** AI cost per active learner, cache-hit trend — operational severity (ADR-058).

### Alternatives Considered
1. **No baseline requirement.** Rejected — "production-ready" becomes subjective.
2. **Cost as a monthly finance report only.** Rejected — SAD §60; cost anomalies must page like incidents.

### Pros
- Objective production-readiness gate.
- Cost visible operationally (protects unit economics).

### Cons
- Metric pipeline to operate.

### Trade-offs
We accept metric-pipeline operation in exchange for **objective health + cost visibility**.

### Consequences
- A module without baseline metrics isn't production-ready (CI/release gate).
- Cost anomalies page on-call (ADR-058).

### Risks
- **Metric cardinality explosion** (per-learner labels) — mitigated by aggregating/label discipline.

### Migration Strategy
Greenfield.

### Related Documents
CTO Constitution §22, §46; SAD §60; SDD §47; ADR-033, ADR-036, ADR-058.

### Future Revisions
- Long-term metric storage/rollups at scale.

---

## ADR-036: Monitoring

| Field | Value |
|---|---|
| **Status** | Accepted |
| **Date** | 2026-07-29 |
| **Related** | ADR-033, ADR-035, ADR-058 |

### Context
The CTO Constitution (§22) and SAD (§60) require monitoring with the AI-cost dashboard at availability-incident severity. Monitoring turns metrics into actionable alerts and dashboards.

### Problem Statement
Metrics without alerting are a graveyard of dashboards no one watches. Without tiered alerting, the team gets alert fatigue or misses real incidents. Without cost-anomaly alerting, a runaway AI spend becomes a business emergency before anyone notices. We need tiered alerting tied to the metric baseline, including cost.

### Decision
**Adopt tiered alerting (page / ticket / info) tied to the baseline metrics + cost anomalies, with the AI-cost dashboard paged at availability-incident severity; SLO-based alerting per critical user journey (tighter SLO for the core review loop than peripheral features).**

- **Tiers:** page (availability/cost-anomaly/security), ticket (degradation/cache-hit drift), info (deploy/flag).
- **Cost-anomaly paging (SAD §60):** AI cost anomalies page on-call like availability incidents.
- **SLO-based (CTO §35):** per-journey SLOs (core review loop tighter than teacher dashboard).
- **Cache-hit-rate drift** ticketed (early warning of cost-curve degradation, ADR-019/058).

### Alternatives Considered
1. **Single alert tier.** Rejected — fatigue or misses.
2. **Cost alerts as tickets only.** Rejected — cost anomalies are business emergencies (SAD §60).
3. **Blanket SLO.** Rejected — peripheral features don't warrant the core loop's tightness (CTO §35).

### Pros
- Actionable alerts; cost anomalies caught fast.
- SLOs reflect journey importance.

### Cons
- Threshold tuning (cache-hit threshold not pre-decided — ADR-058, set from real data).

### Trade-offs
We accept threshold-tuning effort in exchange for **actionable, cost-aware alerting**.

### Consequences
- On-call pages on availability + cost + security; SLO breach alerts per journey.

### Risks
- **Alert fatigue** from wrong thresholds — mitigated by data-driven threshold setting (ADR-058).

### Migration Strategy
Greenfield. Thresholds refined from real data.

### Related Documents
CTO Constitution §22, §35; SAD §60; SDD §47; ADR-033, ADR-035, ADR-058.

### Future Revisions
- Anomaly-detection (ML-based) alerting at scale.

---

## ADR-037: Error Handling

| Field | Value |
|---|---|
| **Status** | Accepted |
| **Date** | 2026-07-29 |
| **Related** | ADR-002, ADR-025, ADR-033, ADR-056 |

### Context
The CTO Constitution (§24) requires errors handled at the layer with context, propagated (never swallowed) when not, honest/specific user-facing errors without leaking internals, and every unhandled exception captured/alerted. The SDD (§19) fixes the typed exception hierarchy and HTTP mapping.

### Problem Statement
Inconsistent error handling (generic exceptions, swallowed errors, stack traces leaked to clients) produces an un-debuggable system and a dishonest UX. Swallowed errors hide defects (P7 violation). We need a typed, predictable error model with honest client responses and complete engineer visibility.

### Decision
**Adopt a typed exception hierarchy (Domain/Application/Infrastructure) with a defined HTTP-status mapping, a single exception renderer producing the standard error response, honest-but-safe client messages (domain-meaningful shown; infrastructure internals not), and every unhandled exception captured/alerted. Errors are never swallowed.**

- **Hierarchy (SDD §19):** `LexiFlowException` → Domain (`InvariantViolation`, `InvalidCommand`, `EntityNotFound`), Application (`Authorization`, `TierGate`), Infrastructure (`ProviderUnavailable`, `RateLimited`).
- **HTTP mapping:** 404/422/409/403/402/429/503/500 per exception type.
- **Honesty vs leakage (CTO §24):** domain-meaningful messages shown; 500s generic (no internals).
- **Renderer:** one component funnels all error paths to the standard response (SDD §29).
- **Never swallowed:** an error a user experiences that engineering never sees is a defect.
- **Domain layer throws no infrastructure exceptions** — Infrastructure wraps persistence/provider failures into typed exceptions.

### Alternatives Considered
1. **Generic `\Exception` everywhere.** Rejected — unpredictable mapping; dishonest UX.
2. **Swallowing errors to "keep going."** Rejected — P7; hides defects.
3. **Stack traces in client responses.** Rejected — leaks internals.
4. **Domain throwing `PDOException`.** Rejected — Infrastructure wraps it.

### Pros
- Predictable, honest, debuggable error model.
- No silently swallowed defects.
- Consistent client error UX.

### Cons
- More exception classes; discipline to map correctly.

### Trade-offs
We accept a richer exception model in exchange for **predictability, honesty, and debuggability**.

### Consequences
- The renderer is tested with an exception→status→payload matrix (ADR-030).
- Every domain exception docblock states when thrown + who catches.

### Risks
- **A new exception unmapped** → 500. Mitigated by the renderer's default-to-500 + matrix test.

### Migration Strategy
Greenfield.

### Related Documents
CTO Constitution §24; SAD §24; SDD §19, §29; ADR-025, ADR-033, ADR-056.

### Future Revisions
- Error-code versioning for external API consumers.

---

## ADR-038: Feature Flags

| Field | Value |
|---|---|
| **Status** | Accepted |
| **Date** | 2026-07-29 |
| **Supersedes** | Legacy ADR-009 (feature-flag service) — renumbered |
| **Related** | ADR-017, ADR-031, ADR-039 |

### Context
The CTO Constitution (§16) and SAD (§69) use feature flags to decouple deployment from release and specifically to gate the Pronunciation module's rollout (ADR-017), letting the open scope/provider ADRs resolve in production reality without risking all users.

### Problem Statement
Without feature flags, deployment = release; an unfinished or risky feature can't ship dark, forcing long-lived branches (CTO §14) or all-or-nothing releases. For the specifically-risky Pronunciation feature (ADR-017), there's no safe way to observe real cost/quality before full launch. We need a flag mechanism that enables dark launches and gradual rollout.

### Decision
**Adopt a feature-flag service (lightweight Redis-backed or managed) read via a `Feature` facade; flags decouple deployment from release, gate risky rollouts (Pronunciation, ADR-017), and enable per-cohort gradual rollout. Flag hygiene (lifecycle, cleanup) is enforced.**

- **Mechanism (legacy ADR-009):** a `Feature` facade wrapping a Redis-backed or managed flag service (ADR-039 config).
- **Decouple deploy/release (CTO §16):** ship dark, turn on independently; makes trunk-based safe.
- **Pronunciation gating (ADR-017):** the v0 ships behind a flag; observe cost/quality; adjust.
- **Hygiene:** flags have an owner + sunset date; stale flags are removed (review gate).

### Alternatives Considered
1. **No flags (deploy = release).** Rejected — forces long branches or all-or-nothing releases.
2. **Inline config-only flags (env vars).** Rejected — can't do per-cohort runtime rollout; restart required.
3. **A heavy enterprise flag platform prematurely.** Rejected — unearned; a lightweight service suffices at MVP (CTO §3).

### Pros
- Safe dark launches; trunk-based viability.
- Risky features (Pronunciation) observed in production before full release.
- Per-cohort rollout.

### Cons
- Flag hygiene discipline (sprawl risk).

### Trade-offs
We accept flag-hygiene discipline in exchange for **safe, decoupled releases**.

### Consequences
- Pronunciation is flag-gated at MVP (ADR-017); flags are reviewed for lifecycle in ADR-055.

### Risks
- **Flag sprawl / dead flags.** Mitigated by owner + sunset + cleanup gate.

### Migration Strategy
Greenfield. Managed flag platform if needs grow.

### Related Documents
CTO Constitution §16; SAD §69; SDD §21, §48; ADR-017, ADR-031, ADR-039.

### Future Revisions
- Managed flag platform; progressive-delivery (canary) at scale.

---

## ADR-039: Configuration Management

| Field | Value |
|---|---|
| **Status** | Accepted |
| **Date** | 2026-07-29 |
| **Supersedes** | Legacy ADR-009 (flag service config half) — folded in |
| **Related** | ADR-021, ADR-032, ADR-040 |

### Context
The CTO Constitution (§17) requires environment-specific config in env vars / a config service (never hardcoded, never duplicated), with a single documented source-of-truth catalog. The SDD (§21) fixes per-module `config/*.php` + a root catalog.

### Problem Statement
Scattered inline magic values and undocumented config produce a system no one can reason about environmentally. Secrets mixed with config risk leakage. We need named, documented, defaulted, env-overridable config, with secrets separated.

### Decision
**Adopt Laravel `config/` (shape) + env vars (values) + a root configuration catalog (`docs/configuration-catalog.md`); secrets are NOT config — they live in the secrets manager (ADR-040); a typed config wrapper validates at boot (fail-fast). Every value is named, documented, defaulted, env-overridable.**

- **Per-module `config/<module>.php`** + root catalog (CTO §17) — review-blocking to keep current.
- **Secrets ≠ config (ADR-040):** `config/` references secret *keys*, never values.
- **Typed wrapper (SDD §21):** validates expected types/ranges at boot; fail-fast on misconfig.
- **No `env()` outside `config/*.php`** (Laravel best practice; enables config caching).

### Alternatives Considered
1. **Inline magic values.** Rejected — undocumented; CTO §17.
2. **Secrets in `.env` committed to VCS.** Rejected — leakage (ADR-040).
3. **A separate config service prematurely.** Rejected — env vars + catalog suffice at MVP (CTO §3).

### Pros
- Discoverable, reviewable config; fail-fast on misconfig.
- Secrets cleanly separated.

### Cons
- Catalog maintenance discipline.

### Trade-offs
We accept catalog maintenance in exchange for **discoverable, safe configuration**.

### Consequences
- New config requires a catalog entry in the same PR; CI checks catalog↔config drift.

### Risks
- **Catalog drift** — mitigated by CI check.

### Migration Strategy
Greenfield. A config service only if env-var count/manageability demands it.

### Related Documents
CTO Constitution §17; SAD §68; SDD §21; ADR-032, ADR-040.

### Future Revisions
- Dynamic config service at scale.

---

## ADR-040: Secrets Management

| Field | Value |
|---|---|
| **Status** | Accepted |
| **Date** | 2026-07-29 |
| **Supersedes** | Legacy ADR-008 (secrets-manager half) — renumbered |
| **Related** | ADR-013, ADR-014, ADR-016, ADR-024, ADR-039 |

### Context
The CTO Constitution (§19) requires secrets never committed to VCS (enforced by hooks + CI scanning), stored in a dedicated secrets manager with least-privilege scoping and rotation. The SAD (§64) requires provider credentials injected at runtime, never in code/config/logs. The task stack mandates AWS.

### Problem Statement
Committed secrets are the most common breach vector. Secrets scattered across config/env with broad access amplify blast radius (a compromised module shouldn't reach credentials it doesn't need). Unrotated secrets accumulate risk. We need centralized, least-privilege, rotatable secrets.

### Decision
**Adopt AWS Secrets Manager (or Parameter Store SecureString) as the secrets store; secrets injected at runtime, scoped per-module to least privilege (a module sees only its credentials), never in code/config/logs; rotated on a schedule; leaked-secret triggers immediate rotation + blameless review. Pre-commit hooks + CI scanning enforce no-secrets-in-VCS.**

- **Store (legacy ADR-008):** AWS Secrets Manager (managed, rotation support, IAM scoping).
- **Least privilege (CTO §19, SAD §64):** per-module access — a compromised Content Import instance has no path to LLM credentials it doesn't need.
- **Never in code/config/logs (ADR-034):** `config/` references keys; the `RedactingLogger` strips secrets.
- **Rotation + leak response (CTO §19):** scheduled rotation; a leak triggers rotation + blameless review (close the process gap).

### Alternatives Considered
1. **Secrets in `.env` committed to VCS.** Rejected — the canonical breach vector.
2. **Broad shared credentials.** Rejected — maximizes blast radius.
3. **Self-hosted secrets (Vault).** Rejected at MVP — operational burden; AWS managed satisfies CTO §3.
4. **No rotation.** Rejected — risk accumulates (CTO §19).

### Pros
- No committed secrets; least-privilege blast radius; rotation; leak response.
- Managed (low ops).

### Cons
- IAM scoping discipline; rotation operational care.

### Trade-offs
We accept secrets-management operational discipline in exchange for **minimized breach blast radius and no committed secrets**.

### Consequences
- Each module's ServiceProvider fetches only its secrets at boot; CI scans for secrets in VCS.

### Risks
- **Over-broad IAM scoping** — mitigated by per-module policy review (ADR-056).
- **A secret logged despite redaction** — mitigated by ADR-034 architectural redaction.

### Migration Strategy
Greenfield.

### Related Documents
CTO Constitution §19; SAD §64; SDD §21; ADR-013, ADR-034, ADR-039, ADR-056.

### Future Revisions
- Dynamic secret injection (SPIFFE/SPIRE) at extreme scale/multi-service.

---

## ADR-041: Privacy

| Field | Value |
|---|---|
| **Status** | Accepted |
| **Date** | 2026-07-29 |
| **Related** | ADR-025, ADR-034, ADR-042, ADR-056 |

### Context
The CEO Vision (§18) makes privacy a constitutional principle: learner data (especially minors') is never sold, used only to improve the learner's own experience, never exposed to advertising/analytics tooling that could enable targeting. The CTO Constitution (§31) implements this at the engineering level: data minimization, minors' elevated protection, no learner data to ad/marketing systems (a hard architectural constraint, not policy). The SAD (§63) enforces it structurally. The Domain Model (§16) makes "no learner data to ad systems" an invariant.

### Problem Statement
Privacy as a policy is brittle; a single tool adoption or a single careless integration leaks data. Minors' data (school features, near-certain) needs elevated handling and a documented legal basis. We need privacy enforced architecturally — structurally impossible to violate — not merely policy-discouraged.

### Decision
**Adopt privacy-by-architecture: data minimization (collect only what's needed), minors' data elevated (tagged + stricter access/retention + parental-consent flow), and the no-data-to-ad-systems invariant enforced by never granting the Delivery/Generic ad path read access to Learner Model data at the infrastructure permission level (structurally impossible, not policy). Data subject access/deletion supported by design.**

- **Minimization (CTO §31):** no speculative collection "in case useful."
- **Minors elevated (SAD §63):** under-18 tagged (ADR-024); stricter access/retention; parental-consent flow before school features ship (legal basis reviewed with counsel).
- **No-data-to-ads invariant (Domain Model §16, CEO Vision §20):** the Delivery/Generic path that could constitute ad/marketing infra has **no read access** to Learner Model data at the permission level — the violation is structurally impossible.
- **Data subject rights (CTO §32):** deletion/export supported by design (the data model allows locating + deleting a learner's data completely).

### Alternatives Considered
1. **Privacy as policy only.** Rejected — brittle; one integration leaks data; CEO Vision §20 is non-negotiable.
2. **Collecting data speculatively.** Rejected — data minimization (CTO §31); unused data is a liability.
3. **Minors handled same as adults.** Rejected — elevated legal/ethical stakes (CTO §31); requires parental consent.
4. **"Aggregated/anonymized data is fine to sell."** Rejected — CEO Vision §20 explicitly forbids, including aggregated/anonymized.

### Pros
- Privacy violations structurally impossible (not policy-discouraged).
- Minors protected; data-subject rights supported.
- Trust preserved (core to the B2B2C channel, CEO Vision §7).

### Cons
- Constrains which third-party tools are acceptable (no ad-enabling analytics) — accepted as a hard constraint.
- Legal review needed for minors' handling before school features.

### Trade-offs
We accept tool-selection constraints and legal-review cost in exchange for **structurally-enforced privacy** — a non-negotiable per CEO Vision §20.

### Consequences
- The permission model is the enforcement mechanism (ADR-025/040); ad-path components cannot read Learner Model data.
- Minors' features blocked on legal review + parental-consent flow.

### Risks
- **A future integration granted overly-broad read access.** Mitigated by ADR-056 security review + the structural permission rule.
- **Minors' legal basis unresolved** before school features — flagged as a blocking open item (final review).

### Migration Strategy
Greenfield.

### Related Documents
CEO Vision §18, §20; CTO Constitution §31; SAD §63; Domain Model §16; SDD §24, §63; ADR-025, ADR-034, ADR-042, ADR-056.

### Future Revisions
- Jurisdiction-specific privacy regimes (GDPR-equivalent) as markets expand.

---

## ADR-042: Data Retention

| Field | Value |
|---|---|
| **Status** | Accepted |
| **Date** | 2026-07-29 |
| **Related** | ADR-023, ADR-041, ADR-043 |

### Context
The CTO Constitution (§32) requires explicit retention periods enforced by automated deletion (data the company no longer needs is a liability), and data-subject access/deletion supported by design. The SAD (§63/§77) ties retention to storage lifecycle and minors' elevated handling.

### Problem Statement
Indefinite data retention accumulates liability (legal, cost, breach surface) and contradicts the privacy principle (ADR-041). Manual cleanup is forgetable. We need explicit, automated, per-class retention.

### Decision
**Adopt explicit per-data-class retention periods enforced by automated deletion: pronunciation audio (short), user uploads (deleted on account deletion), transcripts/excerpts (retained while active then expired), audit logs (longer, stricter access), minors' data (shorter/elevated). Deletion is automated (lifecycle rules + jobs), not manual. Account deletion removes a learner's data completely (designed-for, ADR-041).**

- **Per-class retention (CTO §32):** defined per data class; shorter for sensitive/ephemeral, longer for audit.
- **Automated deletion:** S3 lifecycle rules + nightly purge jobs (ADR-023); DB retention jobs.
- **Account deletion (CTO §32):** `RequestAccountDeletion` removes a learner's data completely across modules — designed-for (the data model locates + deletes it).
- **Minors shorter/elevated (SAD §63).**
- **RPO-aware (ADR-043):** tighter RPO for learner progress than for regenerable cached content.

### Alternatives Considered
1. **Indefinite retention.** Rejected — liability; contradicts ADR-041.
2. **Manual cleanup.** Rejected — forgetable; CTO §32 mandates automation.
3. **Uniform retention.** Rejected — classes differ (audit vs. ephemeral); per-class is correct.

### Pros
- Liability minimized; cost controlled; privacy honored.
- Account deletion genuinely complete.

### Cons
- Retention-rule design + automation discipline.

### Trade-offs
We accept retention-engineering cost in exchange for **minimized liability and honored privacy rights**.

### Consequences
- Deletion is automated + tested; account-deletion completeness is a test (ADR-030).

### Risks
- **Over-deletion** (losing needed data) — mitigated by conservative rules + review.
- **Incomplete account deletion** (orphaned data) — mitigated by the completeness test.

### Migration Strategy
Greenfield.

### Related Documents
CTO Constitution §32; SAD §63, §77; SDD §37, §42; ADR-023, ADR-041, ADR-043.

### Future Revisions
- Jurisdiction-specific retention requirements as markets expand.

---

## ADR-043: Backup Strategy

| Field | Value |
|---|---|
| **Status** | Accepted |
| **Date** | 2026-07-29 |
| **Related** | ADR-021, ADR-032, ADR-042, ADR-044 |

### Context
The CTO Constitution (§33) requires automated backups on a defined schedule **with actual periodic restore drills** — "an untested backup is not a backup, it's an assumption." RTO/RPO defined per data class. The SAD (§57) ties DR to tested restore.

### Problem Statement
Backups that are never restore-tested are assumptions, not backups — they fail exactly when needed. A blanket RTO/RPO over-provisions for regenerable data and under-protects irreplaceable progress data. We need automated, drilled, class-aware backups.

### Decision
**Adopt automated backups (managed RDS snapshots + S3 versioning/cross-region for object storage) on a defined schedule, with explicit RPO/RTO per data class (tighter for learner progress than for regenerable cached content), and periodic actual restore drills (not tabletop).**

- **Automated (CTO §33):** RDS automated snapshots + point-in-time recovery; S3 versioning + lifecycle + cross-region for critical objects.
- **Class-aware RPO/RTO:** learner progress (Mastery) — tight RPO (near-zero loss tolerance, the moat); cached explanations — looser (regenerable).
- **Drills (CTO §33):** periodic actual restore tests (not discussion); a drill that fails is a defect.
- **IaC-defined (ADR-032):** backup config is Terraform; recovered environments are reproducible.

### Alternatives Considered
1. **Untested backups.** Rejected — assumptions, not backups (CTO §33).
2. **Blanket RPO/RTO.** Rejected — over/under-provisions by class.
3. **Manual backups.** Rejected — forgetable; automated required.
4. **No object-storage backup (S3 "is" the backup).** Rejected — versioning + cross-region protect against deletion/corruption.

### Pros
- Tested, class-aware, automated — actually restorable.
- Progress data (the moat) tightly protected.

### Cons
- Drill operational cadence + cost.

### Trade-offs
We accept drill cadence/cost in exchange for **backups that actually restore**.

### Consequences
- Restore drills are scheduled + tracked; RPO/RTO per class documented.

### Risks
- **A drill revealing an un-restorable backup** — the *point* of drilling; treated as a defect to fix, not a failure of the process.

### Migration Strategy
Greenfield.

### Related Documents
CTO Constitution §33; SAD §57; ADR-021, ADR-032, ADR-042, ADR-044.

### Future Revisions
- Cross-region backup replication (1M+).

---

## ADR-044: Disaster Recovery

| Field | Value |
|---|---|
| **Status** | Accepted |
| **Date** | 2026-07-29 |
| **Related** | ADR-032, ADR-043, ADR-045 |

### Context
The CTO Constitution (§37) requires a documented, tested DR plan before it's needed: scoped disaster scenarios, the step-by-step recovery process, who executes it — tested via actual drills (not tabletop), same rigor as backup drills (§33).

### Problem Statement
A DR plan that has never been executed is a hope, not a plan (CTO §37). Untested, undocumented recovery means an actual regional outage or data-corruption event becomes an existential crisis executed from memory. We need a documented, drilled DR plan.

### Decision
**Adopt a documented, drilled DR plan: scoped scenarios (region outage, data corruption, compromised-credential data loss), step-by-step runbooks, named executors, tested via periodic actual drills (not tabletop). Single-region/multi-AZ at launch (sufficient for MVP scope); full multi-region DR deferred to the 1M+ stage where real availability needs justify it.**

- **Scope (CTO §37):** region outage, corruption, credential-compromise data loss.
- **Runbooks:** step-by-step, named executors, contact tree.
- **Drills:** periodic actual execution (not discussion); same rigor as ADR-043.
- **MVP topology:** single-region, multi-AZ (CTO §36) — sufficient; multi-region DR at 1M+ (ADR-045).
- **RTO/RPO** from ADR-043 per class.

### Alternatives Considered

1. **Undocumented DR ("we'll figure it out").** Rejected — a hope, not a plan (CTO §37).
2. **Tabletop-only drills.** Rejected — CTO §37 mandates actual execution.
3. **Multi-region active-active at MVP.** Rejected — CTO §36; unearned complexity/cost.

### Pros
- A real, tested recovery path; named owners; scoped scenarios.
- MVP-appropriate (multi-AZ) without over-engineering.

### Cons
- Drill cadence + runbook maintenance.

### Trade-offs
We accept DR-drill discipline in exchange for **a recovery path that actually works when needed**.

### Consequences
- DR runbooks in `docs/`; drills scheduled + reviewed.

### Risks
- **Scenario not in scope actually occurs.** Mitigated by periodic scope review.
- **Drill surfaces a gap** — the point; treated as a fix-item.

### Migration Strategy
Greenfield. Multi-region DR added at 1M+ (ADR-045).

### Related Documents
CTO Constitution §36, §37; SAD §57; ADR-032, ADR-043, ADR-045.

### Future Revisions
- Multi-region active-active DR (10M, SAD §53).

---

## ADR-045: Scaling Strategy

| Field | Value |
|---|---|
| **Status** | Accepted |
| **Date** | 2026-07-29 |
| **Related** | ADR-006, ADR-019, ADR-021, ADR-046, ADR-058 |

### Context
The CTO Constitution (§34) defines a staged scaling plan (100 → 10M users) — architecture follows organizational/real need, not anticipation. The SAD (§53) maps stages to components, with a low-risk extraction path (Pronunciation/Content Import first, Core Domain last).

### Problem Statement
Building for 10M at the 100-user stage is the classic over-architecture failure (YAGNI, CTO §2); building nothing for scale is the under-investment failure. We need a staged plan that responds to *measured* need at each order of magnitude, preserving seams without paving them prematurely.

### Decision
**Adopt the CTO §34 / SAD §53 staged scaling plan: 100 (monolith) → 1k (async hot paths) → 10k (shared cache mandatory, cost dashboards) → 100k (selective extraction: Pronunciation, Content Import) → 1M (read replicas, distributed broker, multi-region latency) → 10M (distributed services where justified, multi-region active-active). Each stage responds to real, measured need; seams are preserved structurally (ADR-005/006/046) so each transition is a contained change.**

- **Stages (SAD §53):** monolith → async → cache-mandatory → selective extraction → read replicas/multi-region → distributed/active-active.
- **Measured-need gated (CTO §34):** no stage is built speculatively; each responds to real load/team signals.
- **Extraction sequence (ADR-046):** Pronunciation (isolated) and Content Import (bursty) first; Core Domain last.
- **Cost is a scaling axis (ADR-058):** AI cost, not just user-count infra, drives stage decisions (cache hit rate, tier limits).

### Alternatives Considered
1. **Build for 10M at launch.** Rejected — over-architecture (YAGNI, CTO §2); complexity the team can't operate.
2. **No scaling plan.** Rejected — under-investment; painful reactive rewrites.
3. **Uniform extraction (all modules at once).** Rejected — SAD §53; selective, evidence-based extraction is safer.

### Pros
- Right-sized infrastructure at each stage; contained transitions.
- Cost (not just infra) is a first-class scaling axis.
- Extraction sequence protects the Core Domain.

### Cons
- Stage-gate discipline (resisting premature scaling under pressure).

### Trade-offs
We accept stage-gate discipline in exchange for **right-sized, contained scaling**.

### Consequences
- Each stage gate is a review point (real measured need required); cost dashboards become blocking at 10k.

### Risks
- **Premature extraction/scaling under pressure** — mitigated by the measured-need gate (ADR-054).
- **Cache-hit-rate shortfall** degrading the cost curve earlier than modeled — mitigated by week-one monitoring (ADR-058).

### Migration Strategy
Greenfield. Transitions per SAD §53.

### Related Documents
CTO Constitution §34; SAD §53; SDD §1 (P8); ADR-006, ADR-019, ADR-021, ADR-046, ADR-058.

### Future Revisions
- Each stage transition is itself reviewed (may spawn extraction ADRs).

---

## ADR-046: Microservice Migration Strategy

| Field | Value |
|---|---|
| **Status** | Accepted |
| **Date** | 2026-07-29 |
| **Related** | ADR-005, ADR-006, ADR-008, ADR-045 |

### Context
The SAD (§53) defines the extraction sequence; the modular monolith (ADR-006) exists *so that* extraction is possible later without a rewrite. The CTO Constitution (§34) gates extraction on real team-size/deploy-cadence friction, not anticipation.

### Problem Statement
If the monolith's modules aren't real boundaries (ADR-005), extraction is a rewrite. If extraction happens too early (before boundaries are proven in prod), a still-uncertain boundary is frozen as a network contract. If the Core Domain is extracted first, its tight internal coupling (ADR-002) makes the split hardest where it should be easiest to defer. We need an extraction strategy that is staged, evidence-gated, and ordered to minimize risk.

### Decision
**Adopt a staged, evidence-gated microservice migration: extract modules only when real team-size/deploy-cadence friction justifies it (CTO §34), in the order Pronunciation first (already isolated, distinct profile), then Content Import (bursty/background-heavy), with the Core Domain (Learner Model + Scheduling) deliberately LAST — because its internal coupling is a feature until evidence says otherwise, and because the Learner Model/Scheduling boundary (ADR-002) should be proven in prod before freezing it as a network contract. Extraction is a deployment change, not a logic change, because cross-module integration is event-based (ADR-008) and repositories are ports (ADR-010).**

- **Evidence-gated (CTO §34):** measured deploy-cadence friction or load profile, not anticipation.
- **Order (SAD §53):** Pronunciation → Content Import → … → Core Domain last.
- **Low-risk mechanics:** event-based integration (ADR-008) means a module's external shape is already split-ready; repository ports (ADR-010) mean storage moves with the module; the Event Bus (ADR-012) swaps transport.
- **Core Domain last:** its coupling (ADR-002) is a feature at MVP; extract only after the boundary is production-proven.

### Alternatives Considered
1. **Extract everything at once at a stage.** Rejected — high-risk; SAD §53 favors selective, evidence-based extraction.
2. **Extract the Core Domain first** (it's "most important").** Rejected — its coupling is a feature until proven otherwise; extracting an uncertain boundary freezes it prematurely (SAD §53).
3. **No extraction path designed.** Rejected — the monolith exists to enable extraction; designing the seam is the point (ADR-006).
4. **Premature extraction under "microservices are better" pressure.** Rejected — CTO §34; architecture follows organizational need.

### Pros
- Extraction is a contained, low-risk deployment change (not a rewrite).
- Order minimizes risk (isolated/distinct modules first; coupled core last).
- Evidence-gated — no premature distributed-systems complexity.

### Cons
- Requires the seams to be maintained (discipline) even while monolithic.

### Trade-offs
We accept seam-maintenance discipline in exchange for **a low-risk, evidence-gated path out of the monolith**.

### Consequences
- Each extraction is its own ADR (superseding none, recording the trigger/mechanism).
- The Core Domain stays in-process longest.

### Risks
- **Seam erosion** before extraction (a module quietly re-coupling) — mitigated by architecture tests (ADR-005) + ADR-054 review.
- **Premature extraction** — mitigated by the evidence gate.

### Migration Strategy
This ADR *is* the migration strategy. Each extraction = a new ADR + deployment change.

### Related Documents
SAD §53; CTO Constitution §34; SDD §1 (P8); ADR-002, ADR-005, ADR-006, ADR-008, ADR-045.

### Future Revisions
- Each extraction supersedes/extends with its own record.

---

## ADR-047: Coding Standards

| Field | Value |
|---|---|
| **Status** | Accepted |
| **Date** | 2026-07-29 |
| **Supersedes** | Legacy ADR-012 (file-naming mapping, Conflict C-2) — folded in |
| **Related** | ADR-003, ADR-004, ADR-048, ADR-055 |

### Context
The CTO Constitution (§7–§10) sets folder/file/coding standards. The SDD (§8) realizes them in Laravel/PHP/Vue/TS and resolves Conflict C-2 (kebab-case vs. PSR-4 PascalCase) by preserving the Constitution's *intent* while honoring PHP/PSR-4 mandates.

### Problem Statement
Without enforced standards, formatting/naming/organization become review debate (wasted time) and the codebase fragments into competing styles unreadable to the next engineer or AI agent. The kebab-case-vs-PSR-4 tension (Conflict C-2) needs a documented resolution. We need automated, non-debatable standards.

### Decision
**Adopt enforced coding standards: PSR-12 + Laravel Pint (formatting, non-negotiable/automated), PHPStan max + strict rules (static analysis, 100% type coverage in Domain), Vue `<script setup lang="ts">` strict + ESLint/Prettier, OpenAPI-generated TS types only, Value Objects `readonly final`, no public setters on aggregates, comments explain WHY, centralized constants/config. Conflict C-2 resolved (legacy ADR-012): PHP class files follow PSR-4 PascalCase (PHP mandate); non-class asset files follow kebab-case; the Constitution §7 intent (domain-first, descriptive, co-located tests) is preserved.**

- **PHP:** PSR-12 + Pint (preset laravel) — formatting automated, zero debate (CTO §10).
- **Static analysis:** PHPStan max + strict + larastan; 100% Domain type coverage.
- **Frontend:** Vue SFCs `<script setup lang="ts">`, strict TS, ESLint/Prettier; Tailwind tokens centralized.
- **Value discipline:** VOs `readonly final`; aggregates private-constructor + named constructors; no public setters.
- **Conflict C-2 (legacy ADR-012):** PSR-4 PascalCase for PHP class files (mandate); kebab-case for non-class assets; intent preserved; documented as ADR.
- **Comments explain why (CTO §10);** error messages actionable; magic values centralized (ADR-039).

### Alternatives Considered
1. **No enforced formatter (manual formatting).** Rejected — review debate; CTO §10 mandates automation.
2. **kebab-case for PHP class files** (literal Constitution §8 reading).** Rejected — PSR-4 mandates PascalCase; the conflict is resolved by preserving intent, not contradicting PHP (Conflict C-2).
3. **Hand-written TS types for server contracts.** Rejected — drift; generated only (ADR-029).
4. **Public setters on aggregates.** Rejected — bypasses invariants (ADR-004).

### Pros
- Zero formatting debate; consistent, AI-legible codebase.
- Type safety catches the silent-wrong-guess class (CTO §45).
- Conflict C-2 documented, not silently absorbed.

### Cons
- Initial annotation/tooling setup.

### Trade-offs
We accept tooling setup + discipline in exchange for **a consistent, debate-free, type-safe codebase**.

### Consequences
- CI enforces Pint/PHPStan/ESLint/Prettier as fail-fast stages; pre-commit hooks for fast local feedback.

### Risks
- **Standard drift** (a team ignoring a rule) — mitigated by CI enforcement.

### Migration Strategy
Greenfield.

### Related Documents
CTO Constitution §7–§10; SDD §8; ADR-003, ADR-004, ADR-029, ADR-048, ADR-055.

### Future Revisions
- A custom linter enforcing Ubiquitous-Language term usage if drift observed.

---

## ADR-048: AI Coding Rules

| Field | Value |
|---|---|
| **Status** | Accepted |
| **Date** | 2026-07-29 |
| **Related** | ADR-004, ADR-049, ADR-054, ADR-055, ADR-056 |

### Context
The CTO Constitution (§45) — the AI Coding Assistant Constitution — is "the most consequential section" given how heavily the org builds with AI assistants (Claude Code, Cursor, Copilot). It is binding rules, not suggestions: never rename/restructure boundaries without an ADR; never duplicate existing logic; always update docs; always explain trade-offs; never guess on ambiguity; never silently touch auth/payments/PII; smallest correct diff; read surrounding patterns first; AI changes reviewed with equal rigor.

### Problem Statement
AI coding assistants, left ungoverned, introduce the most damaging, hardest-to-detect defects: silent architecture drift, undetected logic duplication, confident wrong guesses that compile and pass tests. A sweeping AI refactor bundled into a feature makes review impossible. We need binding, reviewable rules governing AI-authored changes.

### Decision
**Adopt the CTO Constitution §45 rules as binding for all AI-authored/assisted changes, enforced via the review checklists (ADR-055) and architecture review (ADR-054): no boundary/term changes without ADR; search-before-write (no duplicated logic); docs-in-same-change; explain trade-offs; block-and-ask on ambiguity; flag auth/payments/PII for elevated review (ADR-056); smallest correct diff; match existing patterns; human review with equal (or greater) rigor.**

- **Binding rules (CTO §45):** each is a review gate, not a suggestion.
- **Enforcement:** the SDD §52 review checklist explicitly confirms the AI constitution was followed; architecture review (ADR-054) catches boundary drift.
- **Failure-mode focus:** the specific dangers (silent drift, duplication, confident wrong guesses, bundled refactors) argue for *more* deliberate review of AI changes, not less.
- **Architecture tests (ADR-005)** catch what review might miss (dependency-rule violations).

### Alternatives Considered
1. **Treat AI changes as lower-risk (faster review).** Rejected — the specific failure modes argue the opposite (CTO §45).
2. **Allow AI to refactor toward its preference inside unrelated tasks.** Rejected — silent architecture drift; must propose + ADR.
3. **Allow AI to guess on ambiguity.** Rejected — a blocked task with a question beats a shipped wrong guess (CTO §45).

### Pros
- Prevents the hardest-to-detect defect classes AI introduces.
- Keeps AI productivity without sacrificing integrity.

### Cons
- Slightly higher review overhead on AI-assisted PRs (the right cost).

### Trade-offs
We accept higher review rigor on AI changes in exchange for **preventing silent drift/duplication/wrong-guesses**.

### Consequences
- Every AI-assisted PR states the rules followed; reviewers verify (ADR-055).

### Risks
- **Review fatigue** leading to rubber-stamping AI diffs — mitigated by the explicit checklist + architecture tests as backstop.

### Migration Strategy
Greenfield.

### Related Documents
CTO Constitution §45; SDD §50, §52; ADR-004, ADR-049, ADR-054, ADR-055, ADR-056.

### Future Revisions
- Tooling that auto-detects duplicated logic (CTO §45) as it matures.

---

## ADR-049: Dependency Policy

| Field | Value |
|---|---|
| **Status** | Accepted |
| **Date** | 2026-07-29 |
| **Related** | ADR-003, ADR-024, ADR-048, ADR-050 |

### Context
The CTO Constitution (§3, §20) requires new dependencies evaluated against the selection framework before adoption (not pulled in casually because an AI/tutorial suggested it), and dependency health audited on a recurring cadence — every dependency is code the company is responsible for.

### Problem Statement
Uncontrolled dependency growth ("dependency creep") introduces maintenance burden, security surface, and license risk — much of it from AI assistants or tutorials suggesting packages casually. Stale/unmaintained dependencies become vulnerabilities. We need a deliberate adoption + recurring audit policy.

### Decision
**Adopt a deliberate dependency policy: every new dependency is evaluated against the CTO §3 framework (team fluency, operational maturity, TCO, reversibility, ecosystem health; rejection if it requires unacquired expertise, poor maintenance, data lock-in, or solves a problem not yet had) and recorded in an ADR if non-trivial; dependency health (maintenance, security advisories, licenses) audited on a recurring cadence; the codebase is kept lean.**

- **Adoption gate (CTO §3/§20):** framework evaluation before adoption; non-trivial deps get an ADR.
- **Recurring audit (CTO §20):** maintenance activity, security advisories (Dependabot/Renovate + SCA), license compatibility — on a defined cadence.
- **Rejection criteria (CTO §3):** unacquired operational expertise; single-maintainer/stale/unclear-license; data/logic lock-in; solves a non-problem (YAGNI).
- **Lean codebase:** "impressive" is never sufficient justification.

### Alternatives Considered
1. **Casual adoption (pull whatever an AI/tutorial suggests).** Rejected — CTO §20; creep + risk.
2. **No recurring audit.** Rejected — vulnerabilities accumulate undetected.
3. **Vendoring everything (no deps).** Rejected — reinvents proven solutions; the framework allows justified deps.

### Pros
- Controlled, audited, license-safe dependency surface.
- Security vulnerabilities caught on cadence.

### Cons
- Adoption friction (the right kind — deliberate).

### Trade-offs
We accept adoption friction in exchange for **a controlled, auditable, secure dependency surface**.

### Consequences
- New deps require evaluation + (if non-trivial) an ADR; SCA runs in CI; audit on schedule.

### Risks
- **A critical dep goes unmaintained** — mitigated by the audit + reversibility preference (ADR-003).

### Migration Strategy
Greenfield.

### Related Documents
CTO Constitution §3, §20; SDD §8; ADR-003, ADR-024, ADR-048.

### Future Revisions
- Automated license/SCA policy enforcement tightening.

---

## ADR-050: Technical Debt Policy

| Field | Value |
|---|---|
| **Status** | Accepted |
| **Date** | 2026-07-29 |
| **Related** | ADR-017, ADR-049, ADR-054 |

### Context
The CTO Constitution (§48) requires technical debt tracked explicitly (not implicit in engineers' memory), logged at the point the shortcut is taken, triaged like backlog, weighed against feature work using the decision framework (does it threaten the content-agnostic core, or is it contained/low-risk). The Pronunciation v0 (ADR-017) is a live candidate for logged debt.

### Problem Statement
Implicit technical debt (lives in engineers' heads) is forgotten until an unlucky engineer hits it, with no record of what was traded or what paydown requires. Undifferentiated debt (no triage) crowds out feature work or, worse, threatens the core. We need explicit, logged, triaged debt.

### Decision
**Adopt explicit technical-debt tracking: every deliberate shortcut is logged at the point taken (what was traded, what paydown requires), triaged against the decision framework (threatens the content-agnostic core boundary, or contained/low-risk), and weighed against feature work in prioritization. The Pronunciation v0 gap (ADR-017) is logged debt with its upgrade path.**

- **Log at point of taking (CTO §48):** what was traded, what paydown requires — not discovered later.
- **Triage (CTO §48):** core-threatening vs. contained; core-threatening prioritized.
- **Weighed against features (Product Strategy §47 extended):** debt is backlog, prioritized by the framework.
- **Pronunciation v0 (ADR-017):** logged debt — the v0→full gap with its upgrade path recorded.

### Alternatives Considered
1. **Implicit debt (in memory).** Rejected — forgotten; CTO §48.
2. **No triage (all debt equal).** Rejected — core-threatening debt must win prioritization.
3. **Never taking deliberate shortcuts.** Rejected — unrealistic; the point is logging them, not avoiding all.

### Pros
- Debt visible, prioritized, paydown-path-known.
- Core-protecting prioritization.

### Cons
- Logging discipline at shortcut-time.

### Trade-offs
We accept logging discipline in exchange for **debt that's visible, triaged, and paydown-ready**.

### Consequences
- A debt registry (in the tracker) with triage tags; reviewed in ADR-054.

### Risks
- **Debt forgotten despite logging** — mitigated by triage review cadence.

### Migration Strategy
Greenfield.

### Related Documents
CTO Constitution §48; Product Strategy §47; SDD §50; ADR-017, ADR-049, ADR-054.

### Future Revisions
- Debt-burden metrics (e.g., trend of core-threatening debt) as the codebase grows.

---

## ADR-051: Versioning Strategy

| Field | Value |
|---|---|
| **Status** | Accepted |
| **Date** | 2026-07-29 |
| **Related** | ADR-008, ADR-011, ADR-015, ADR-027 |

### Context
The CTO Constitution (§15) mandates SemVer for external consumers and explicit internal-contract versioning. The SAD (§70) requires breaking internal changes (e.g., event-shape changes) to be deliberate/reviewed/visible. Versioning spans the public API (ADR-027), internal module contracts, Domain Events (ADR-011), and prompt templates (ADR-015).

### Problem Statement
Without explicit versioning, a breaking change to an event shape or internal contract silently breaks consumers; a prompt/cache-key version bump missed serves stale content; public-API consumers have no stability contract. We need versioning across all contract surfaces, with breaking changes as deliberate events.

### Decision
**Adopt a multi-surface versioning strategy: SemVer for the public API once external consumers exist (ADR-027); internal module contracts versioned via declarations + event shapes (additive = non-breaking; breaking = deliberate, reviewed, cross-consumer); Domain Events versioned (ADR-011); prompt templates + cache-key versions coupled (ADR-015/019). Breaking changes are never silent; they're reviewed events across all affected consumers.**

- **Public API:** SemVer (ADR-027).
- **Internal contracts:** module declarations + events; breaking = reviewed across consumers (SAD §70).
- **Domain Events:** versioned; additive non-breaking (ADR-011).
- **Prompts/cache:** version-coupled so quality changes invalidate correctly (ADR-015/019).
- **Ubiquitous Language (Domain Model §21):** terms version like APIs — meaning doesn't silently drift; a genuine change is documented/communicated cross-team.

### Alternatives Considered
1. **No internal versioning (only public SemVer).** Rejected — silent internal breakage.
2. **Breaking changes without review.** Rejected — ripple damage across consumers.
3. **Decoupled prompt/cache versions.** Rejected — stale content after a quality change (CEO Vision §7).

### Pros
- No silent breakage on any surface; stability contracts for consumers.
- Cache correctness via coupled versions.

### Cons
- Version discipline across surfaces.

### Trade-offs
We accept multi-surface version discipline in exchange for **no silent breakage and stable contracts**.

### Consequences
- A breaking change triggers ADR-054 review across consumers; cache keys bump with prompt/model changes.

### Risks
- **Missed version bump** — mitigated by auto-derived versions (template/model hash).

### Migration Strategy
Greenfield.

### Related Documents
CTO Constitution §15; SAD §70; Domain Model §21; ADR-008, ADR-011, ADR-015, ADR-027.

### Future Revisions
- Contract-driven consumer compatibility testing at scale.

---

## ADR-052: Migration Strategy

| Field | Value |
|---|---|
| **Status** | Accepted |
| **Date** | 2026-07-29 |
| **Related** | ADR-021, ADR-031, ADR-046 |

### Context
The CTO Constitution (§45) requires a migration strategy for any change affecting existing data/contracts. The SAD (§71) makes schema changes ship with an explicit data-integrity plan, especially for the Core Domain. The SDD (§31) fixes expand/contract + module-namespaced migrations.

### Problem Statement
Naive migrations (in-place rewrites of hot tables, empty `down()`, altering historical immutable data) cause outages, lock contentions, data corruption, and unrecoverable deploys — especially catastrophic in the Core Domain (a silent Mastery data-integrity issue is among the most damaging defects). We need a safe, reversible, zero-downtime migration discipline.

### Decision
**Adopt a safe migration discipline: module-namespaced Laravel migrations with functional `down()`; expand-then-contract for zero-downtime (expand → backfill via queued job → switch → contract); a mandatory data-integrity plan for any Core-Domain schema change (what changes, how existing rows are handled, post-migration assertion, rollback path); historical immutable data (transcripts) is never altered in place — versioning handles evolution; large backfills are resumable queued jobs.**

- **Module-namespaced migrations** (SDD §31): ownership visible in the filename.
- **Expand/contract (SDD §31):** zero-downtime; avoids lock-heavy in-place rewrites.
- **Core Domain integrity plan (SAD §71):** mandatory for Mastery/aggregate-shape changes; no "too small to skip."
- **Immutability (Domain Model §16):** transcript reprocessing = new version, never in-place mutation.
- **Functional `down()`** (rollback is real); large backfills resumable.

### Alternatives Considered
1. **In-place rewrites of hot tables.** Rejected — locks/outages.
2. **Empty `down()`.** Rejected — unrecoverable.
3. **Altering historical immutable data.** Rejected — Domain Model §16.
4. **Skipping the integrity plan for "small" Core changes.** Rejected — SAD §71; too costly to risk.

### Pros
- Safe, reversible, zero-downtime schema evolution.
- Core Domain integrity protected.

### Cons
- More migration steps (expand/contract).

### Trade-offs
We accept more steps in exchange for **safe, reversible, zero-downtime migrations**.

### Consequences
- A pre-deploy `migrate --pretend` in staging surfaces statements; Core migrations are a hard PR gate.

### Risks
- **A lock-heavy migration slipping through** — mitigated by staging pretend + online-DDL preference.

### Migration Strategy
This ADR *is* the migration strategy.

### Related Documents
CTO Constitution §45; SAD §71; Domain Model §16; SDD §31; ADR-021, ADR-031, ADR-046.

### Future Revisions
- Online schema-change tooling (e.g., pg-osc) at scale if needed.

---

## ADR-053: Architecture Governance

| Field | Value |
|---|---|
| **Status** | Accepted |
| **Date** | 2026-07-29 |
| **Related** | ADR-001, ADR-054, ADR-055, ADR-056 |

### Context
The CTO Constitution (§11/§12) establishes Documentation First + ADR culture; §43–§44 set review checklists. The SDD (§0.3) fixes the conflict protocol. Governance is what keeps the architecture honest as the team and codebase grow.

### Problem Statement
Without governance, decisions drift from the ADRs, boundaries erode, and the immutable documents' intent is quietly lost — the system decays toward an unmaintainable state regardless of how good the initial design was. We need standing governance that keeps decisions binding and boundaries enforced.

### Decision
**Adopt lightweight, continuous architecture governance: the ADR collection is the binding decision log (this document); significant decisions require an ADR; the conflict protocol (explain → ADR → proceed) is binding; architecture tests (ADR-005) enforce rules in CI; architecture review (ADR-054), engineering review (ADR-055), and security review (ADR-056) are the human gates; the immutable documents remain the source of truth above this register.**

- **ADR = binding (CTO §12):** decisions are followed unless superseded.
- **Conflict protocol (SDD §0.3):** explain, ADR, proceed — never silently resolve.
- **CI enforcement:** architecture tests (ADR-005) catch rule violations automatically.
- **Human gates:** ADR-054/055/056 review processes.
- **Immutable docs above ADRs:** business/domain/architecture decisions in Levels 0–2 are not reopened by this register.

### Alternatives Considered
1. **No governance (decisions as folklore).** Rejected — drift; the system decays.
2. **Heavy architecture board bottleneck.** Rejected — slows iteration; lightweight continuous governance is sufficient at this scale.
3. **ADRs as optional documentation.** Rejected — they must be binding to function.

### Pros
- Decisions stay binding; boundaries enforced; drift caught.
- Lightweight enough not to bottleneck.

### Cons
- Requires discipline + the review processes to function.

### Trade-offs
We accept governance overhead in exchange for **architectural integrity over time**.

### Consequences
- New significant decisions → ADR; conflicts → the protocol; CI + reviews enforce.

### Risks
- **Governance bypass under deadline pressure** — mitigated by CI enforcement (can't bypass a failing architecture test) + review checklists.

### Migration Strategy
Greenfield.

### Related Documents
CTO Constitution §11, §12, §43–§44; SDD §0.3; ADR-001, ADR-054, ADR-055, ADR-056.

### Future Revisions
- Formal architecture review board at larger org size.

---

## ADR-054: Architecture Review Process

| Field | Value |
|---|---|
| **Status** | Accepted |
| **Date** | 2026-07-29 |
| **Related** | ADR-005, ADR-053, ADR-055 |

### Context
The CTO Constitution (§43) sets the architecture review checklist: applied to any change touching a service boundary or introducing a dependency — does it preserve the content-agnostic core; does it need an ADR; does it introduce coupling that makes a future split harder; what does it cost at the next real scale milestone; has a conflict with immutable docs been checked.

### Problem Statement
Without an architecture review gate, boundary/dependency changes slip through code review (which focuses on correctness/style), silently eroding the seams and the core boundary (ADR-001). We need a defined trigger and process for architecture-significant changes.

### Decision
**Adopt an architecture review process triggered by: any change touching a module boundary, introducing a dependency, altering an aggregate/event/contract, or affecting a scaling assumption. The review applies the CTO §43 checklist (preserve the core; ADR needed; coupling/extraction impact; cost at next scale stage; immutable-doc conflict check). Outcome: approve, require-ADR, or block.**

- **Triggers:** boundary/dependency/aggregate/event/contract/scaling-assumption changes.
- **Checklist (CTO §43):** core preservation; ADR need; coupling/extraction impact; next-stage cost; conflict check.
- **Outcomes:** approve / require-ADR-first / block.
- **AI-assisted changes (ADR-048):** architecture review is where silent AI drift is caught.

### Alternatives Considered
1. **Code review covers architecture.** Rejected — code review focuses on correctness/style; architecture needs a dedicated lens.
2. **Review only major changes.** Rejected — boundary erosion happens in small changes.
3. **No conflict check against immutable docs.** Rejected — the §0.3 protocol requires it.

### Pros
- Boundary/core/seam preservation; ADRs triggered when needed; conflicts surfaced.

### Cons
- Review overhead on triggered changes (the right cost).

### Trade-offs
We accept architecture-review overhead in exchange for **preserved boundaries and the core**.

### Consequences
- Triggered changes get architecture review; the dependency graph is reviewed here (ADR-005).

### Risks
- **Trigger missed** (a boundary change not flagged) — mitigated by architecture tests as backstop + reviewer awareness.

### Migration Strategy
Greenfield.

### Related Documents
CTO Constitution §43; SDD §52; ADR-001, ADR-005, ADR-053, ADR-055.

### Future Revisions
- Tooling that auto-detects architecture-review triggers (boundary/contract changes).

---

## ADR-055: Engineering Review Process

| Field | Value |
|---|---|
| **Status** | Accepted |
| **Date** | 2026-07-29 |
| **Related** | ADR-030, ADR-048, ADR-054, ADR-056 |

### Context
The CTO Constitution (§42) sets the code review checklist (correctness vs criteria; standards adherence; test coverage to risk; security considered; docs updated; AI constitution followed; no secrets/dead-code/scope-creep). §40 sets Definition of Done. The SDD (§52) provides the consolidated review checklist.

### Problem Statement
A review that rubber-stamps diffs (especially AI diffs) lets defects, scope creep, and silent integrity issues through. A review without a checklist forgets categories under time pressure. We need a disciplined, checklist-driven review that's especially rigorous on AI-assisted changes and Core Domain changes.

### Decision
**Adopt a checklist-driven engineering review process (SDD §52): correctness vs acceptance criteria; standards adherence; test coverage appropriate to risk (Core Domain deepest); security explicitly considered; docs updated in-same-change; AI constitution (ADR-048) followed; no secrets/dead-code/scope-creep; for Core Domain, behavior matches pedagogical intent. Reviewers treat AI-assisted changes with equal-or-greater rigor. A Definition of Done gate (CTO §40) is required to merge.**

- **Checklist (SDD §52):** the consolidated gate.
- **Risk-weighted tests:** Core Domain deepest (ADR-030).
- **AI rigor (ADR-048):** equal-or-greater review on AI changes.
- **Definition of Done (CTO §40):** merged to main; tests pass; docs updated; staged/verified; Core Domain intent confirmed.
- **Core Domain intent check:** behavior matches PRD/Product Strategy pedagogical intent, not just "compiles."

### Alternatives Considered
1. **Rubber-stamp review.** Rejected — defects/scope-creep slip through.
2. **Checklist-free review.** Rejected — categories forgotten under pressure.
3. **Lighter review for AI changes.** Rejected — ADR-048; the opposite is correct.

### Pros
- Defects/scope-creep/integrity-issues caught; Core Domain protected; AI drift caught.

### Cons
- Review thoroughness takes time (the right cost).

### Trade-offs
We accept review time in exchange for **quality and integrity gates**.

### Consequences
- No merge without checklist + DoD; AI changes flagged for the constitution check.

### Risks
- **Review bottleneck** slowing delivery — mitigated by small PRs (trunk-based, ADR-031) + fast CI.

### Migration Strategy
Greenfield.

### Related Documents
CTO Constitution §40, §42, §45; SDD §52; ADR-030, ADR-048, ADR-054, ADR-056.

### Future Revisions
- Async review tooling / review-SLOs as the team grows.

---

## ADR-056: Security Review Process

| Field | Value |
|---|---|
| **Status** | Accepted |
| **Date** | 2026-07-29 |
| **Related** | ADR-024, ADR-025, ADR-040, ADR-041, ADR-048 |

### Context
The CTO Constitution (§44) sets the security review checklist for anything touching authentication, authorization, payments, or PII: OWASP Top 10 baseline; least-privilege; secrets handling; minors' data explicit sign-off. Security is "a property of every design decision, not a pre-launch checklist" (CTO §28).

### Problem Statement
Security treated as a pre-launch audit (rather than per-decision) accumulates risk that's expensive to fix late. Changes to auth/payments/PII without elevated review are where the most costly subtle bugs (human or AI) land. Minors' data needs sign-off beyond standard review. We need an elevated, triggered security review.

### Decision
**Adopt an elevated security review process triggered by any change touching auth, authz, payments, or PII: OWASP Top 10 baseline explicitly checked; least-privilege confirmed; secrets handling confirmed (ADR-040); minors' data explicit sign-off (beyond standard review). AI changes touching these categories are flagged for the elevated path (ADR-048) — never silently touched. Security is evaluated at every architectural decision point (ADR-053/054), not as a pre-launch audit.**

- **Triggers:** auth/authz/payments/PII changes.
- **OWASP baseline (CTO §28/§44):** explicitly checked, not assumed.
- **Least-privilege (CTO §44):** confirmed for new service-to-service permissions.
- **Secrets (ADR-040):** handling confirmed.
- **Minors (CTO §44/§31):** explicit sign-off beyond standard review.
- **AI flagging (ADR-048):** AI changes to these categories route to elevated review, never silently.
- **Per-decision, not pre-launch (CTO §28):** security is a property of every design decision.

### Alternatives Considered
1. **Pre-launch security audit only.** Rejected — CTO §28; accumulates expensive-to-fix risk.
2. **Standard review for security-sensitive changes.** Rejected — these categories need elevated rigor (CTO §44).
3. **No minors-specific sign-off.** Rejected — elevated legal/ethical stakes (CTO §31).

### Pros
- Security risk caught per-change, not late; minors protected; AI security changes flagged.

### Cons
- Elevated-review overhead on triggered changes (the right cost).

### Trade-offs
We accept elevated-review overhead in exchange for **security as a continuous property, not a late audit**.

### Consequences
- Triggered changes require security sign-off; minors' changes require extra sign-off; AI security changes can't slip through.

### Risks
- **A security-sensitive change not flagged** — mitigated by reviewer awareness + the audit log (anomalies detectable).

### Migration Strategy
Greenfield.

### Related Documents
CTO Constitution §28, §31, §44; SDD §54; ADR-024, ADR-025, ADR-040, ADR-041, ADR-048.

### Future Revisions
- Threat-modeling cadence; pen-testing schedule as the product matures.

---

## ADR-057: Performance Budgets

| Field | Value |
|---|---|
| **Status** | Accepted |
| **Date** | 2026-07-29 |
| **Related** | ADR-018, ADR-019, ADR-036, ADR-058 |

### Context
The CTO Constitution (§25) sets explicit per-interaction performance budgets derived from the PRD (§38): sub-3-second word/sentence explanation, content import acknowledgment under 1s (processing async), flashcard review interaction under 500ms, pronunciation scoring feedback under 3s (flagged as a challenge). Performance is profile-driven, not intuition-driven.

### Problem Statement
Without explicit budgets, "performance" is subjective and optimized reactively ("feels slow" → guess). Without per-interaction targets, the cache/async architecture (ADR-018/020) has nothing to optimize against. We need explicit, measured budgets that drive the architecture.

### Decision
**Adopt explicit per-interaction performance budgets: explanation ≤3s (cache-first, mostly ≪ that), import acknowledgment ≤1s (processing async), review interaction ≤500ms, pronunciation feedback ≤3s (async path). Performance work is profile-driven (CTO §25); budgets are part of the Definition of Done for affected changes; latency is monitored per the SLOs (ADR-036).**

- **Budgets (CTO §25):** the four per-interaction targets, each with an architectural mechanism (cache, async).
- **Profile-driven (CTO §25):** "feels slow" triggers profiling, not guesswork.
- **DoD inclusion:** affected changes must meet budgets (ADR-055).
- **SLO monitoring (ADR-036):** p95/p99 against budgets.

### Alternatives Considered
1. **No explicit budgets.** Rejected — subjective; reactive; CTO §25.
2. **Uniform latency target.** Rejected — interactions differ (explanation vs. review vs. import).
3. **Intuition-based optimization.** Rejected — CTO §25 mandates profiling.

### Pros
- Objective targets driving the cache/async architecture.
- Per-interaction appropriateness; profile-driven fixes.

### Cons
- Budget enforcement discipline on changes.

### Trade-offs
We accept budget-discipline in exchange for **objective, achievable performance**.

### Consequences
- Budget violations are defects; the cache (ADR-018/019) and async (ADR-020) are the primary mechanisms.

### Risks
- **Pronunciation budget hard to meet** (ASR latency) — flagged (CTO §25); mitigated by async path (ADR-017/020).

### Migration Strategy
Greenfield.

### Related Documents
CTO Constitution §25; SAD §54; PRD §38; SDD §27, §53; ADR-018, ADR-036, ADR-058.

### Future Revisions
- Tighter budgets as the cache matures; mobile-specific budgets (V2).

---

## ADR-058: Cost Monitoring

| Field | Value |
|---|---|
| **Status** | Accepted (Conditional) |
| **Date** | 2026-07-29 |
| **Supersedes** | Legacy ADR-015 (cache-hit threshold) — folded in |
| **Related** | ADR-013, ADR-018, ADR-019, ADR-035, ADR-036 |

### Context
The CTO Constitution (§22) treats AI inference cost with the same rigor as uptime; the SAD (§60) makes the AI-cost dashboard page on-call like an availability incident; Product Strategy (§44) makes the cost-per-active-learner curve the business's core unit-economics. The SAD (§87) declines to pre-decide the acceptable cache-hit-rate threshold — it must come from real MVP data.

### Problem Statement
AI cost is the dominant scaling risk (CTO final review). Without operational (not just financial) cost visibility, a runaway spend or a cache-hit collapse becomes a business emergency before anyone notices. Without a defined healthy threshold, "is the cost curve OK?" is unanswerable. We need cost-as-incident monitoring and a data-derived health threshold.

### Decision
**Adopt cost monitoring as first-class operational health: per-request/per-provider cost tracked at the AI/Speech Gateways; AI cost per active learner + cache-hit-rate + provider-call-rate as first-class metrics (ADR-035); cost anomalies page on-call like availability incidents (ADR-036); the cache-only circuit breaker (ADR-013) protects spend under anomaly/outage. The acceptable cache-hit-rate threshold is NOT pre-decided — it is derived from real MVP usage data and then set as a health gate (the conditional element).**

- **Cost-as-incident (SAD §60):** cost anomalies page on-call.
- **First-class metrics (ADR-035):** cost/active-learner, cache-hit-rate, provider-call-rate.
- **Threshold conditional (SAD §87):** the acceptable hit-rate is set from real data post-launch, not guessed now.
- **Spend protection (ADR-013):** cache-only fallback caps spend under anomaly.
- **Trend matters:** cache-hit should rise as the flywheel spins (Product Strategy §9); a falling trend is an early warning.

### Alternatives Considered
1. **Cost as a monthly finance report.** Rejected — SAD §60; too slow; a runaway is an emergency by then.
2. **Pre-decided threshold.** Rejected — SAD §87; would be a guess without usage data; conditional on real data is honest.
3. **No spend-protection circuit breaker.** Rejected — a provider anomaly could unlimitedly spend.

### Pros
- Cost visible operationally; anomalies caught fast; spend capped.
- Data-derived threshold (honest, not guessed).

### Cons
- Threshold undefined until post-launch data (a deliberate, documented gap, not an oversight).

### Trade-offs
We accept a temporary undefined threshold (filled by data) in exchange for **honest, operational cost control**.

### Consequences
- Cost pages on-call; cache-hit trend monitored from week one (not discovered at 10k users, SAD §84).
- The threshold becomes a health gate once set.

### Risks
- **Cache-hit lower than modeled** (high content diversity) → cost curve degrades. Mitigated by week-one monitoring + tier limits (ADR-025) + tiered models (ADR-014); the threshold, once set, quantifies the risk.

### Migration Strategy
Post-launch: collect cache-hit/cost data → set the threshold ADR → make it a health gate.

### Related Documents
CTO Constitution §22, §46; SAD §38, §60, §84, §87; Product Strategy §9, §44; SDD §33, §47; ADR-013, ADR-018, ADR-019, ADR-035, ADR-036.

### Future Revisions
- Per-cohort/per-tier cost attribution at scale.

---

## ADR-059: Future Language Expansion

| Field | Value |
|---|---|
| **Status** | Proposed |
| **Date** | 2026-07-29 |
| **Supersedes** | Legacy ADR-005 (second-L1 parametrization) — renumbered |
| **Related** | ADR-004, ADR-015, ADR-019 |

### Context
The CEO Vision (§16) sequences expansion: Bangla first (win decisively), then Hindi (engine reuse, population scale), then Urdu/Indonesian/Arabic — each funded by the prior's proven economics. The Domain Model (§20, §23) flags an open question: does "Bangla-aware Translation/Explanation" become "L1-aware" cleanly (parametrization), or does it require a context split? This is the legacy second-L1 parametrization ADR (legacy ADR-005), explicitly a pre-Hindi decision.

### Problem Statement
If Linguistic Analysis and Curriculum Alignment are hard-coded to Bangla, the second-L1 expansion requires invasive rework. If they're over-abstracted prematurely for many L1s, the Bangla quality (the launch differentiator) suffers from premature generality. We must decide, *before* the Hindi effort, whether to parametrize by L1 or split contexts — but this is genuinely a "Proposed" decision: it should be resolved with real evidence, not speculatively now.

### Decision
**Adopt a "parametrize, don't split" working hypothesis for L1 expansion: design Linguistic Analysis and Curriculum Alignment as L1-parametrized (the cache keys already include L1, ADR-019; prompts are L1-aware, ADR-015), preferring parametrization over context-split unless evidence during the Hindi effort proves a split is needed. Status is Proposed — this is resolved deliberately during the first real expansion, not preemptively.**

- **Working hypothesis:** parametrize by L1 (cache keys L1-versioned, ADR-019; prompts L1-aware, ADR-015; curriculum mapping L1-parametrized).
- **Evidence-driven resolution:** the Hindi effort validates or refutes the hypothesis; a split is available if parametrization proves insufficient.
- **Cache-correctness preserved:** the content cache is already L1-keyed, so a second L1 cannot collide with Bangla (ADR-019).
- **Not built prematurely (YAGNI, CTO §2):** only the *seam* (L1 parametrization) is preserved; multi-L1 features aren't built ahead of need.

### Alternatives Considered
1. **Hard-code Bangla (decide later).** Rejected — invasive rework at expansion; the seam should be preserved now.
2. **Over-abstract for many L1s now.** Rejected — premature generality risks Bangla quality (the launch differentiator); YAGNI.
3. **Context split per L1.** Rejected as the default — heavier; reserved if parametrization proves insufficient.
4. **Decide definitively now.** Rejected — genuinely evidence-dependent (Domain Model §23); Proposed status is honest.

### Pros
- Preserves the expansion seam cheaply (L1 parametrization) without premature multi-L1 features.
- Cache-correctness already L1-safe.
- Reversible (split available if parametrization insufficient).

### Cons
- A hypothesis, not a certainty — the Hindi effort may reveal a split is needed.

### Trade-offs
We accept a hypothesis (resolvable later) over a speculative definitive decision, preserving the seam at low cost — the honest call for an evidence-dependent question.

### Consequences
- Linguistic Analysis/Curriculum are designed L1-parametrized; the Hindi effort validates.
- Bangla quality is not sacrificed to premature generality.

### Risks
- **Parametrization insufficient** at Hindi → a context split needed (a real, resolvable rework, not a rewrite). Mitigated by evaluating early in the Hindi effort.

### Migration Strategy
During Hindi expansion: validate parametrization; if insufficient, split via a superseding ADR.

### Related Documents
CEO Vision §16; Domain Model §20, §23; SAD §88; SDD §40, §59; ADR-004, ADR-015, ADR-019.

### Future Revisions
- Supersede with a definitive parametrize-vs-split ADR after the Hindi effort's evidence.

---

## ADR-060: Architecture Evolution Strategy

| Field | Value |
|---|---|
| **Status** | Accepted |
| **Date** | 2026-07-29 |
| **Related** | ADR-005, ADR-046, ADR-053, ADR-054 |

### Context
The CTO Constitution (§50) names the three highest-leverage decisions for decade-scale maintainability: the content-agnostic core/adapter boundary; disciplined documentation + ADR culture; scale discipline (next real order of magnitude, not speculative). The Domain Model (§21) defines the evolution strategy: extend bounded contexts / add new ones at established seams, never silently stretch an existing context past its boundary.

### Problem Statement
Without an evolution strategy, the architecture either (a) ossifies (can't accommodate new content types/L1s/features without rewrites) or (b) erodes (contexts stretched past boundaries, seams broken, the system decaying toward an unmaintainable state). We need a principled rule for how the architecture evolves.

### Decision
**Adopt the evolution strategy from Domain Model §21 + CTO §50: the architecture evolves by extending bounded contexts and adding new ones at established seams (never by silently stretching a context past its boundary); the content-agnostic core/adapter boundary is preserved (new content types = new adapters, not core changes); ADR culture + documentation preserve institutional reasoning past any individual's tenure; scale discipline builds for the next real order of magnitude, not a speculative one. New needs that don't fit an existing context → model a new context or revisit the Context Map via ADR.**

- **Extend at seams (Domain Model §21):** new content types = new adapters; new contexts (e.g., Conversation, Domain Model §20) added at seams, not stretched into existing ones.
- **Core/adapter boundary (CTO §50/§1):** the content-agnostic core never imports adapter-specific code; this is what lets a content type ship in a sprint and an L1 in a quarter.
- **ADR + docs culture (CTO §11/§12/§50):** preserves the *why* past individual tenure — code can be rewritten, lost reasoning can't.
- **Scale discipline (CTO §34/§50):** next real order of magnitude only; speculative generality rejected.
- **New-context rule:** a need that fits no existing context → new context (ADR), not quiet stretching.

### Alternatives Considered
1. **Stretch existing contexts to fit new needs.** Rejected — Domain Model §21; destroys boundaries; the decay path.
2. **Rewrite-on-evolution.** Rejected — the seams exist specifically to avoid rewrites.
3. **Speculative generality (build for every future now).** Rejected — YAGNI (CTO §2); complexity the team can't operate.
4. **No ADR/docs culture (oral tradition).** Rejected — reasoning lost past tenure (CTO §50).

### Pros
- The architecture accommodates evolution (content types, L1s, features) without rewrites.
- Institutional reasoning survives turnover.
- Speculative complexity is resisted.

### Cons
- Discipline to extend-at-seams rather than stretch (review-gated, ADR-054).

### Trade-offs
We accept evolution-discipline in exchange for **an architecture that stays healthy and evolvable over a decade**.

### Consequences
- New needs trigger "does this fit a context or need a new one?" → ADR if new (ADR-053/054).
- The core/adapter boundary is the most-protected seam (ADR-001).

### Risks
- **Stretch-under-pressure** (a deadline forcing a quick hack into the wrong context) — mitigated by ADR-054 review + ADR-050 debt logging (log the stretch as debt if truly unavoidable, with a paydown plan).

### Migration Strategy
Greenfield. This ADR is the standing evolution rule.

### Related Documents
CTO Constitution §1, §34, §50; Domain Model §20, §21; SAD §1; SDD §57; ADR-001, ADR-005, ADR-046, ADR-053, ADR-054.

### Future Revisions
- Each major evolution (new content type, new L1, new context) is its own ADR under this strategy.

---

# Part III — Final Review

## Architecture Readiness Assessment

This ADR collection, together with the seven immutable predecessor documents, brings LexiFlow AI from "architecture and design complete" to "decisions formalized and implementation-ready." The assessment below is honest: it credits what is resolved and names what is not.

### What is now fully resolved
- **Architecture & style:** Modular monolith (ADR-006), Hexagonal per module (ADR-003), DDD (ADR-004), module organization (ADR-005), CQRS selective (ADR-007), event-driven + outbox + bus (ADR-008/009/011/012).
- **AI/Speech:** Gateway ACLs (ADR-013/016), provider strategy (ADR-014), prompt versioning (ADR-015), pronunciation v0 scope (ADR-017).
- **Data/infra:** cache strategy (ADR-018/019), queue (ADR-020), PostgreSQL (ADR-021), storage (ADR-023).
- **Security/identity:** auth (ADR-024), authz (ADR-025), RBAC (ADR-026), secrets (ADR-040), privacy (ADR-041).
- **API/contracts:** versioning (ADR-027), REST (ADR-028), OpenAPI-first (ADR-029).
- **Delivery/quality/observability:** testing (ADR-030), CI/CD (ADR-031), deploy (ADR-032), observability/logging/metrics/monitoring (ADR-033–036), errors (ADR-037), flags (ADR-038), config (ADR-039).
- **Resilience/scale:** retention (ADR-042), backup (ADR-043), DR (ADR-044), scaling (ADR-045), microservice migration (ADR-046).
- **Culture/governance:** coding standards (ADR-047), AI rules (ADR-048), dependencies (ADR-049), debt (ADR-050), versioning (ADR-051), migrations (ADR-052), governance + reviews (ADR-053–056).
- **Non-functional/evolution:** performance (ADR-057), cost monitoring (ADR-058), evolution (ADR-060).

### What remains explicitly open (honest gaps, not oversights)
1. **ADR-002 (Core Domain boundary)** — Accepted (Conditional); the combined Learner Model/Scheduling module is provisional pending production validation at the 100k stage.
2. **ADR-014 / ADR-016 (provider identities)** — Accepted (Conditional); specific LLM/ASR vendors await final confirmation against GTM usage data (the GTM plan remains the document series' outstanding pre-implementation dependency).
3. **ADR-058 (cache-hit threshold)** — Accepted (Conditional); the acceptable threshold is derived from real MVP data, not pre-decided.
4. **ADR-059 (L1 expansion)** — Proposed; resolved during the Hindi effort, not preemptively.
5. **Minors' data legal basis** — a non-ADR open item: parental-consent/legal review is required before school features ship (ADR-041), depending on counsel, not engineering.

These are deliberately honest "not yet" items, each with a defined resolution path and a working default the system implements against. None blocks implementation of the well-resolved modules.

---

## Decision Quality Score

**Overall Decision Quality: 89 / 100**

### Scoring rationale
- **Comprehensiveness (95/100):** all 60 mandated decisions are addressed; every legacy ADR (Domain Model/SAD/SDD) is reconciled into the master scheme with zero loss (§0.3).
- **Consistency with immutable docs (96/100):** every ADR traces to Levels 0–2; two detected conflicts (Eloquent purity [Conflict C-1], kebab-case/PSR-4 [Conflict C-2]) and one numbering conflict are handled per the Constitution §12 protocol — explained, ADR'd, proceeded on existing decisions. None silently resolved.
- **WHY-depth (90/100):** each ADR explains why alternatives lost, what debt it introduces, and the migration path — the required enterprise quality bar.
- **Reversibility posture (92/100):** the overwhelming majority of decisions preserve a documented reversal path (provider swaps, store swaps, extraction, scope-agnostic pronunciation) — the property that keeps the system adaptable.
- **Honesty about conditionality (88/100):** five items are explicitly conditional/proposed rather than falsely "Accepted" — intellectual honesty that prevents false confidence.

### Why not higher
The score is held below 90 by the genuinely conditional items (provider identities, cache-hit threshold, L1 expansion) that depend on data/events not yet available (the still-missing GTM plan). These are correct to leave open, but they cap *definitive* readiness. Additionally, the minors' legal-basis dependency is a non-engineering gate that no ADR can close.

---

## Remaining Open Decisions

| # | Open decision | Owner of resolution | Resolution trigger |
|---|---|---|---|
| 1 | Confirm/revise the combined Learner Model/Scheduling module (ADR-002) | Architect + production data | 100k-user stage evidence |
| 2 | Final LLM vendor identity + tiered-model thresholds (ADR-014) | Architect + GTM data | GTM plan + early MVP traffic |
| 3 | Final ASR/pronunciation vendor (ADR-016) | Architect + GTM data | GTM plan + early MVP traffic |
| 4 | Cache-hit-rate health threshold (ADR-058) | Architect + data | Post-launch cache/cost data |
| 5 | L1 parametrize-vs-split (ADR-059) | Architect + Hindi effort | First L1 expansion evidence |
| 6 | Minors' data legal basis / parental consent (ADR-041) | Legal counsel | Before school features ship |
| 7 | (External) Go-to-Market / Phase 1 Launch Plan | Product/Business | Blocks #2/#3 finalization |

---

## Future ADR Candidates

These are not yet needed but are anticipated; each will be raised when its trigger occurs:
- **F-1:** Dedicated message broker selection (Kafka vs SNS+SQS vs Redis Streams) — at the extraction stage (ADR-012/020/046).
- **F-2:** Vector database / embeddings store — if embeddings become load-bearing (SAD §36/§37; ADR-021/022).
- **F-3:** Dedicated search engine — when cross-user content discovery is prioritized (ADR-022).
- **F-4:** Self-hosted open-weight LLM for the cheapest tier — at scale once team/GPU expertise justifies (ADR-014).
- **F-5:** Self-hosted ASR — at scale for the cheapest tier (ADR-016).
- **F-6:** Event sourcing for the Core Domain — at extreme scale / audit need (ADR-007/009).
- **F-7:** Multi-region active-active DR — at the 10M stage (ADR-044/045).
- **F-8:** GraphQL — at the API-licensing stage with diverse consumers (ADR-028).
- **F-9:** Enterprise SSO (SAML/OIDC) — Year 4 institutional (ADR-024).
- **F-10:** ABAC (attribute-based access control) — Year 4 fine-grained enterprise policies (ADR-026).
- **F-11:** Pronunciation full-scoping (superseding ADR-017) — once v0 validates demand + a quality provider is confirmed.

---

## Implementation Readiness Score

**Implementation Readiness: 91 / 100**

### Rationale
This is the highest readiness score in the document series (Domain Model 78 → SAD 81 → SDD 86 → ADRs 91) because the ADR collection closes the last layer of implementation-blocking uncertainty: the *decisions* the predecessors deferred are now formalized. A senior engineer or AI coding agent can implement the well-resolved modules (Identity, Content Import, Linguistic Analysis, all Generic modules, the skeleton/scaffold/CI) against binding, WHY-explained decisions with no architectural decisions of their own to make.

It is not higher than 91 because:
- Five conditional/proposed items (provider identities, cache-hit threshold, L1 expansion, Core boundary validation) remain genuinely data/decision-dependent.
- The minors' legal basis is a non-engineering gate.
- The still-missing GTM plan is the upstream dependency for finalizing provider choices — a document-series gap, not an ADR gap.

### What can begin immediately
Skeleton + scaffold + architecture tests + CI + environments; Identity; Content Import; Linguistic Analysis; all Generic modules. These have zero open ADRs blocking them.

### What proceeds on a working default
Core Domain (Learner Model + Scheduling) on the ADR-002 combined-module default; Pronunciation behind a flag (ADR-017); provider integrations on the Gateway-abstraction default (vendor identity configurable).

### What must wait
Minors-touching school features (legal basis, ADR-041); definitive provider contracts (GTM data); the L1 expansion (Hindi effort).

---

## Architecture Risk Matrix

| Risk | Likelihood | Impact | Risk score | Primary mitigation (ADR) |
|---|---|---|---|---|
| Learner Model/Scheduling boundary wrong at scale | Medium | High | **High** | ADR-002 (conditional, event-ready external shape) |
| AI cost curve falls short of model (low cache-hit) | Medium | High | **High** | ADR-018/019/058 (week-one monitoring, tier limits, tiered models) |
| ASR provider volatility / quality on Bangla-accented English | High | Medium | **High** | ADR-016/017 (isolation, v0 scope, swappability) |
| Silent AI-driven architecture drift / logic duplication | High | Medium | **High** | ADR-005/048/054/055 (architecture tests, AI rules, reviews) |
| Privacy invariant violation (minors'/data to ads) | Low | Critical | **High** | ADR-025/034/041 (structural enforcement, redaction, permissions) |
| "AI Tutor" reified as a service | Medium | Medium | **Medium** | ADR-004/005 (explicit guardrail) |
| Platform dependency (YouTube API/ToS change) | Medium | Medium | **Medium** | ADR-005/023 (adapter seam, embed stance, monitoring) |
| Billing dark-pattern creep under growth pressure | Medium | Medium | **Medium** | ADR-046 (CEO Vision §20 values guardrail) |
| Premature complexity / over-architecture | Medium | Medium | **Medium** | ADR-006/045/060 (YAGNI, staged scaling) |
| Dual-write event loss | Low | High | **Medium** | ADR-009 (transactional outbox) |
| Cross-module coupling (extraction-blocker) | Medium | Medium | **Medium** | ADR-005 (architecture tests) |
| Translation-quality trust failure (Bangla) | Medium | High | **High** | ADR-015 (eval-gated prompts) |
| Secrets leak | Low | Critical | **High** | ADR-034/040 (redaction + secrets manager) |
| Untested backup / DR plan | Medium | High | **High** | ADR-043/044 (drills) |
| Dependency supply-chain vulnerability | Medium | Medium | **Medium** | ADR-049 (adoption gate + recurring audit) |

---

## Top 10 Engineering Risks

1. **AI cost curve** dependent on a real-world cache-hit rate not yet measured (ADR-018/019/058).
2. **Learner Model/Scheduling boundary** provisional; a wrong call risks Core-Domain rework (ADR-002).
3. **ASR provider volatility + Bangla-accent quality** — the most volatile, harm-capable dependency (ADR-016/017).
4. **Silent AI-driven drift/duplication** under heavy AI-assisted development (ADR-005/048).
5. **Privacy invariant violation** (minors'/data-to-ads) — low-likelihood, catastrophic-impact (ADR-025/041).
6. **Translation-quality trust failure** in the Bangla-first market — disproportionately damaging (ADR-015).
7. **Platform dependency** (YouTube API/ToS) — external, monitored (ADR-005/023).
8. **Premature complexity / over-architecture** under "we'll need this" pressure (ADR-006/045).
9. **Core Domain schema/integrity bug** from a careless migration — among the most damaging defects (ADR-052/010).
10. **Provider market shock** (pricing/availability/data-terms) on LLM/ASR (ADR-013/014).

---

## Top 10 Architecture Strengths

1. **Content-agnostic core / adapter boundary** — new content types and L1s ship without core rewrites (ADR-001/005/060).
2. **Modular monolith with real, enforced seams** — extraction is a deployment change, not a rewrite (ADR-005/006/046).
3. **Two-cache strategy** — delivers both the cost flywheel and correct personalization (ADR-018/019).
4. **Transactional outbox + idempotent consumers** — guaranteed state/event consistency, broker-ready (ADR-009).
5. **AI/Speech Gateway ACLs** — provider swaps and cost control are single-point, contained (ADR-013/016).
6. **Pure, framework-free Core Domain** — testable, swappable, extraction-safe (ADR-003/010).
7. **Structural privacy enforcement** — invariants impossible to violate, not policy-discouraged (ADR-025/041).
8. **Honest staged scaling + low-risk extraction order** — right-sized at each stage, Core last (ADR-045/046).
9. **Binding ADR culture + conflict protocol** — reasoning survives turnover; conflicts never silently resolved (ADR-053/§0.3).
10. **Risk-weighted testing + Bangla eval gate** — the moat and the trust promise are the most-protected code (ADR-015/030).

---

## Recommendations Before Coding Starts

1. **Stand up the skeleton first** (ADR-006): modular monolith scaffold, the module-scaffold command, architecture tests (ADR-005), CI (ADR-031), four environments (ADR-032). *Gate:* a "hello-world" use case passes all layers + architecture tests green. Nothing else should start before this.
2. **Resolve the GTM plan (external)** — it unblocks definitive provider selection (ADR-014/016). Until then, implement against the Gateway-abstraction default with vendor identity as configuration.
3. **Begin legal review for minors' data** (ADR-041) in parallel with early development, so it doesn't block school features later.
4. **Implement Core Domain (ADR-002) on the combined-module default** but treat its production validation as a tracked item — don't freeze the boundary prematurely.
5. **Ship Pronunciation behind a flag** (ADR-017) — observe cost/quality before committing to full scope or a provider.
6. **Instrument cost + cache-hit from week one** (ADR-058) — the cost curve's health must be measured, not assumed; set the threshold ADR once data exists.
7. **Adopt the ADR culture immediately** (ADR-053): every significant decision gets an ADR; the conflict protocol (§0.3) is binding from the first commit.
8. **Sequence per the SAD §89 / SDD §50 roadmap**, gated on test suites + staging, not calendar dates.

---

### Final Note from the Chief Software Architect

This ADR collection is the decisional spine of LexiFlow AI. It does not reopen a single decision the seven immutable predecessors made; it formalizes — as numbered, WHY-explained, alternatives-rejected, debt-acknowledged, migration-pathed records — the engineering choices required before code. The numbering conflict between the provisional schemes embedded in those predecessors and the canonical 60-ADR scheme mandated here was detected, not hidden: it is resolved by the §0.3 reconciliation table, preserving every legacy reference without loss.

Of sixty decisions, fifty-five are definitive and binding; five are honestly conditional or proposed, each with a working default and a defined resolution path. The architecture is not risk-free — the Top 10 risks are real — but every high-impact risk has a structural mitigation traceable to a specific ADR. The single most important property this collection establishes is **reversibility under discipline**: the overwhelming majority of decisions preserve a documented reversal path, and the ADR culture ensures that when a reversal is warranted, it is a deliberate, reviewed, recorded event — never a quiet drift. That property, more than any single decision, is what keeps a system healthy for the decade the CEO Vision is betting on.

The next action is implementation of the well-resolved modules against these decisions. The decisions are ready. The reasoning is preserved. Code may begin.

---

*End of Architecture Decision Records — LexiFlow AI (ADR-001 → ADR-060).*
