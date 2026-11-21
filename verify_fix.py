#!/usr/bin/env python3
with open('C:\\RODAGEST-main\\resources\\views\\dashboard.blade.php', 'r', encoding='utf8') as f:
    content = f.read()

old_pattern = "{{ asset('images/logo-rodagest.png') }}"
new_pattern = '/images/logo-rodagest'

old_count = content.count(old_pattern)
new_pattern2 = '/images/logo-rodagest.png'
new_count = content.count(new_pattern2)
logo_img_count = content.count('logo-img')

print('Old asset pattern count:', old_count)
print('New path count (/images/logo-rodagest.png):', new_count)
print('logo-img count:', logo_img_count)

# Show lines with logo img
for i, line in enumerate(content.split('\n'), 1):
    if 'logo-img' in line.lower() or 'logo-rodagest' in line.lower():
        print(f'Line {i}: {line.strip()[:120]}')