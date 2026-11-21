#!/usr/bin/env python3
with open('C:\\RODAGEST-main\\resources\\views\\dashboard.blade.php', 'r', encoding='utf8') as f:
    content = f.read()

# Primera ocurrencia: nav barra logo (línea 56)
old1 = '<img src="{{ asset(\'images/logo-rodagest.png\') }}" alt=\"RodaGest Logo\" class=\"h-10 w-auto mix-blend-multiply\">'
new1 = '<img src="/images/logo-rodagest.png" alt=\"RodaGest Logo\" class=\"h-10 w-auto mix-blend-multiply logo-img\">'

# Segunda ocurrencia: sidebar logo (línea 102)
old2 = '<img src="{{ asset(\'images/logo-rodagest.png\') }}" alt=\"RodaGest Logo\" class=\"h-8 w-auto\">'
new2 = '<img src="/images/logo-rodagest.png" alt=\"RodaGest Logo\" class=\"h-8 w-auto\">'

print('old1 match:', old1 in content)
print('old2 match:', old2 in content)

if old1 not in content:
    # Try without escaped inner quotes
    old1a = "<img src=\\'{{ asset(\\'images/logo-rodagest.png\\') }}\\' alt=\\'RodaGest Logo\\' class=\\'h-10 w-auto mix-blend-multiply\\'>"
    print("old1a try")

if old2 not in content:
    old2a = "<img src=\\'{{ asset(\\'images/logo-rodagest.png\\') }}\\' alt=\\'RodaGest Logo\\' class=\\'h-8 w-auto\\'>"
    print("old2a try")

content = content.replace(old1, new1)
content = content.replace(old2, new2)

with open('C:\\RODAGEST-main\\resources\\views\\dashboard.blade.php', 'w', encoding='utf8') as f:
    f.write(content)

print('Dashboard actualizado OK')
print('logo-img count:', content.count('logo-img'))
print('asset count:', content.count('asset'))