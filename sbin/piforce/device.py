import asyncio, evdev

pad = evdev.InputDevice('/dev/input/event0')
screen = evdev.InputDevice('/dev/input/event1')

async def print_events(device):
    async for event in device.async_read_loop():
        print(device.path, evdev.categorize(event), sep=': ')

for device in pad, screen:
    asyncio.ensure_future(print_events(device))

loop = asyncio.get_event_loop()
loop.run_forever()
