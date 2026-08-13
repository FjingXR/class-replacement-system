# FCB-Themed Website UI Color System

> Design inspiration: FC Barcelona official visual identity — blue (blau), garnet (grana), and gold (Senyera).

---

## Brand Core

| Role | Color | HEX | Source |
|---|---|---|---|
| FCB Blue | Deep Royal Blue | `#004D98` | Primary logo blue |
| FCB Garnet | Wine Red | `#A50044` | Signature grana |
| FCB Gold | Senyera Yellow | `#EDBB00` | Catalan flag |

---

## Primary — FCB Blue

| Token | HEX | Usage |
|---|---|---|
| Primary | `#004D98` | Main accent, active nav, key CTAs |
| On Primary | `#FFFFFF` | Text on primary background |
| Primary Light | `#3A7BD5` | Hover state, focus ring |
| Primary Container | `rgba(0, 77, 152, 0.18)` | Nav bar bg, selected row, chip bg |
| On Primary Container | `#6BA3E0` | Text/icons inside container |

---

## Secondary — FCB Garnet

| Token | HEX | Usage |
|---|---|---|
| Secondary | `#A50044` | Secondary accent, important actions |
| On Secondary | `#FFFFFF` | Text on secondary background |
| Secondary Light | `#C4336A` | Hover state |
| Secondary Container | `rgba(165, 0, 68, 0.18)` | Alert banners, highlight chips |
| On Secondary Container | `#E8668A` | Text inside container |

---

## Tertiary — FCB Gold

| Token | HEX | Usage |
|---|---|---|
| Tertiary | `#DBA800` | Accent badges, VIP, gold status |
| On Tertiary | `#1A1000` | Text on gold background |
| Tertiary Light | `#F0C830` | Hover state |
| Tertiary Container | `rgba(219, 168, 0, 0.18)` | Subtle highlights |
| On Tertiary Container | `#DBA800` | Text inside container |

---

## Background — Dark Mode

| Token | HEX | Usage |
|---|---|---|
| BG | `#0D1B2A` | Page background (deep navy) |
| On BG | `#E0E6ED` | Primary text |
| Surface | `#1B2838` | Cards, panels |
| On Surface | `#E0E6ED` | Text on surface |
| Surface Variant | `#243447` | Input bg, table headers |
| On Surface Variant | `#9EAAB8` | Secondary/muted text |

---

## Background — Light Mode

| Token | HEX | Usage |
|---|---|---|
| BG | `#F0F3F7` | Page background (cool off-white) |
| On BG | `#0D1B2A` | Primary text |
| Surface | `#FFFFFF` | Cards, panels |
| On Surface | `#1B2838` | Text on surface |
| Surface Variant | `#E4E8EE` | Input bg, muted sections |
| On Surface Variant | `#5A6978` | Secondary text |

---

## Semantic — Status Colors

### Success

| Token | HEX | Usage |
|---|---|---|
| Success | `#2E7D5A` | Approved, success badges |
| On Success | `#FFFFFF` | Text on success bg |
| Success Container | `rgba(46, 125, 90, 0.18)` | Success background |
| On Success Container | `#81C78A` | Text inside container |

### Warning

| Token | HEX | Usage |
|---|---|---|
| Warning | `#D4880F` | Pending, warning badges |
| On Warning | `#FFFFFF` | Text on warning bg |
| Warning Container | `rgba(212, 136, 15, 0.18)` | Warning background |
| On Warning Container | `#FFB74D` | Text inside container |

### Error

| Token | HEX | Usage |
|---|---|---|
| Error | `#C62828` | Error, rejected, danger |
| On Error | `#FFFFFF` | Text on error bg |
| Error Container | `rgba(198, 40, 40, 0.18)` | Error background |
| On Error Container | `#EF9A9A` | Text inside container |

### Info

| Token | HEX | Usage |
|---|---|---|
| Info | `#004D98` | Informational (same as Primary) |
| On Info | `#FFFFFF` | Text on info bg |
| Info Container | `rgba(0, 77, 152, 0.18)` | Info background |
| On Info Container | `#6BA3E0` | Text inside container |

---

## Borders & Dividers

| Role | HEX (Dark) | HEX (Light) | Usage |
|---|---|---|---|
| Outline | `rgba(155, 170, 190, 0.20)` | `rgba(30, 50, 70, 0.12)` | Default borders |
| Outline Strong | `rgba(155, 170, 190, 0.35)` | `rgba(30, 50, 70, 0.25)` | Emphasized borders |
| Divider | `rgba(155, 170, 190, 0.12)` | `rgba(30, 50, 70, 0.08)` | Section dividers |

---

## Interactive States

| State | Derivation |
|---|---|
| Hover | Primary + 12% lightness shift, or `filter: brightness(1.1)` |
| Active / Pressed | Primary − 8% lightness, `transform: scale(0.97)` |
| Focus | 2px ring in primary at 30% opacity: `2px 0 0 rgba(0, 77, 152, 0.3)` |
| Disabled | Color at 35% opacity, `cursor: not-allowed`, no shadow |

---

## Summary Palette

| Role | Color | HEX |
|---|---|---|
| Primary | Blue | `#004D98` |
| Secondary | Garnet | `#A50044` |
| Tertiary | Gold | `#DBA800` |
| Success | Green | `#2E7D5A` |
| Warning | Amber | `#D4880F` |
| Error | Red | `#C62828` |
| Info | Blue | `#004D98` |
| Surface (Dark) | Navy | `#1B2838` |
| Surface (Light) | White | `#FFFFFF` |
| BG (Dark) | Deep Navy | `#0D1B2A` |
| BG (Light) | Cool Gray | `#F0F3F7` |

---

## Legend Bar Status Mapping

| Status | CSS Variable | Color | HEX | Usage |
|---|---|---|---|---|
| Normal Class | `--color-success` | Green | `#2E7D5A` | Scheduled classes |
| Replacement | `--color-primary` | Blue | `#004D98` | Replacement classes |
| Pending | `--color-warning` | Amber | `#D4880F` | Pending approval |
| Conflict / Holiday | `--color-error` | Red | `#C62828` | Conflicts, public holidays |
